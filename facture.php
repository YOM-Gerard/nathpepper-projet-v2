<?php
session_start();
require_once 'includes/db.php';
require_once 'libs/fpdf.php';

// 1. SÉCURITÉ : L'utilisateur doit être connecté
if (!isset($_SESSION['user_id'])) {
    die("Accès refusé. Veuillez vous connecter.");
}

// 2. RÉCUPÉRATION DE LA COMMANDE
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id <= 0) {
    die("ID de commande invalide.");
}

try {
    // Récupérer les détails de la commande
    $stmt = $pdo->prepare("
        SELECT orders.*, users.name as client_name, users.email as client_email 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id 
        WHERE orders.id = :id
    ");
    $stmt->execute(['id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Commande introuvable.");
    }

    // Vérifier les droits : soit c'est l'admin, soit c'est le client qui a passé la commande
    $stmtCheckAdmin = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id");
    $stmtCheckAdmin->execute(['id' => $_SESSION['user_id']]);
    $currentUser = $stmtCheckAdmin->fetch(PDO::FETCH_ASSOC);
    $is_admin = ($currentUser && $currentUser['is_admin'] == 1);

    if ($_SESSION['user_id'] != $order['user_id'] && !$is_admin) {
        die("Accès refusé. Vous n'avez pas le droit de consulter cette facture.");
    }

    // Récupérer les articles de la commande
    $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
    $stmtItems->execute(['order_id' => $order_id]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// 3. CRÉATION DU PDF AVEC FPDF
class PDF extends FPDF {
    // En-tête de la facture
    function Header() {
        // Nom de la boutique (Style Épuré / Haut de gamme)
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(183, 28, 28); // Rouge brique / couleur poivre
        $this->Cell(100, 10, utf8_decode('NATHPEPPER'), 0, 0, 'L');
        
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(90, 10, utf8_decode('FACTURE PROVISOIRE'), 0, 1, 'R');
        
        // Ligne de séparation de l'en-tête
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, 25, 200, 25);
        $this->Ln(10);
    }

    // Pied de page
    function Footer() {
        $this->SetY(-25);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        // Ligne de séparation
        $this->Line(10, 275, 200, 275);
        // Mentions légales obligatoires
        $this->Cell(0, 5, utf8_decode('Nathpepper E-Commerce SAS - Capital de 5 000 - SIRET : 123 456 789 00012'), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('Merci pour votre confiance ! Des questions ? contact@nathpepper.com'), 0, 1, 'C');
        $this->Cell(0, 5, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

// Initialisation du document
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(50, 50, 50);

// --- BLOC INFOS ENTREPRISE & CLIENT ---
// Infos Émetteur (À gauche)
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(100, 5, utf8_decode('Boutique Nathpepper'), 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 5, utf8_decode('12 rue des Poivres Rares'), 0, 1);
$pdf->Cell(100, 5, utf8_decode('75001 Paris, France'), 0, 1);
$pdf->Cell(100, 5, utf8_decode('Siren : 123 456 789'), 0, 1);

// Infos Client (À droite - Repositionnement du curseur)
$pdf->SetXY($x + 110, $y);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 5, utf8_decode('Facturé à :'), 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->SetX($x + 110);
$pdf->Cell(80, 5, utf8_decode($order['client_name'] ?? 'Achat Invité'), 0, 1);
$pdf->SetX($x + 110);
$pdf->Cell(80, 5, utf8_decode($order['client_email']), 0, 1);

$pdf->Ln(15); // Espace

// --- BLOC DÉTAILS FACTURE ---
$pdf->SetFillColor(245, 245, 245);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 7, utf8_decode('Numéro de Facture'), 1, 0, 'C', true);
$pdf->Cell(45, 7, utf8_decode('Date d\'Émission'), 1, 0, 'C', true);
$pdf->Cell(45, 7, utf8_decode('Mode de Paiement'), 1, 0, 'C', true);
$pdf->Cell(55, 7, utf8_decode('Statut Logistique'), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(45, 7, '#FA-' . str_pad($order['id'], 5, '0', STR_PAD_LEFT), 1, 0, 'C');
$pdf->Cell(45, 7, date('d/m/Y H:i', strtotime($order['created_at'])), 1, 0, 'C');
$pdf->Cell(45, 7, 'Carte Bancaire (Stripe)', 1, 0, 'C');
$pdf->Cell(55, 7, strtoupper(utf8_decode($order['status'])), 1, 1, 'C');

$pdf->Ln(15);

// --- TABLEAU DES ARTICLES ---
// Entête du tableau
$pdf->SetFillColor(33, 33, 33);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(110, 8, utf8_decode('Désignation du produit'), 1, 0, 'L', true);
$pdf->Cell(25, 8, utf8_decode('Prix U. HT'), 1, 0, 'C', true);
$pdf->Cell(20, 8, utf8_decode('Qté'), 1, 0, 'C', true);
$pdf->Cell(35, 8, utf8_decode('Total HT'), 1, 1, 'C', true);

// Lignes d'articles
$pdf->SetTextColor(50, 50, 50);
$pdf->SetFont('Arial', '', 10);

$total_ht_global = 0;

foreach ($items as $item) {
    // Simulation calcul HT/TVA (TVA 5.5% sur l'alimentation en France)
    $taux_tva = 0.055;
    $prix_ttc = $item['price'];
    $prix_ht = $prix_ttc / (1 + $taux_tva);
    $subtotal_ht = $prix_ht * $item['quantity'];
    $total_ht_global += $subtotal_ht;

    $pdf->Cell(110, 8, utf8_decode($item['product_name']), 1, 0, 'L');
    $pdf->Cell(25, 8, number_format($prix_ht, 2, ',', ' ') . ' E', 1, 0, 'C');
    $pdf->Cell(20, 8, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(35, 8, number_format($subtotal_ht, 2, ',', ' ') . ' E', 1, 1, 'C');
}

$pdf->Ln(5);

// --- BLOC TOTALISATEUR ---
$total_ttc = $order['total_amount'];
$total_tva = $total_ttc - $total_ht_global;

$pdf->SetX(120);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 7, 'Total Global HT :', 0, 0, 'R');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 7, number_format($total_ht_global, 2, ',', ' ') . ' E', 1, 1, 'C');

$pdf->SetX(120);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 7, 'TVA (5.5%) :', 0, 0, 'R');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 7, number_format($total_tva, 2, ',', ' ') . ' E', 1, 1, 'C');

$pdf->SetX(120);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(230, 245, 230); // Fond vert clair pour le Net à payer
$pdf->Cell(45, 8, 'Net à Payer TTC :', 0, 0, 'R');
$pdf->Cell(35, 8, number_format($total_ttc, 2, ',', ' ') . ' E', 1, 1, 'C', true);

// Sortie du PDF (Affichage direct dans le navigateur)
$pdf->Output('I', 'Facture_Nathpepper_#' . $order_id . '.pdf');