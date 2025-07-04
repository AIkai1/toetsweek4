<?php
class ProductCrud {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO('sqlite:products.db');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->init_database();
        } catch(PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    private function init_database() {
        $sql = "CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            naam TEXT NOT NULL,
            omschrijving TEXT,
            prijs REAL NOT NULL,
            maat TEXT,
            afbeelding TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->pdo->exec($sql);
    }
    
    public function insert_product($naam, $omschrijving = null, $maat = null, $afbeelding = null, $prijs = null) {
        $sql = "INSERT INTO products (naam, omschrijving, maat, afbeelding, prijs) VALUES (:naam, :omschrijving, :maat, :afbeelding, :prijs)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':omschrijving', $omschrijving);
            $stmt->bindParam(':maat', $maat);
            $stmt->bindParam(':afbeelding', $afbeelding);
            $stmt->bindParam(':prijs', $prijs, PDO::PARAM_INT);
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return ["error" => "Error inserting product: " . $e->getMessage()];
        }
    }
    
    public function get_all_products() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM products ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Error fetching products: " . $e->getMessage()];
        }
    }
    
    public function get_product_by_id($product_id) {
        $sql = "SELECT * FROM products WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $product_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Error fetching product: " . $e->getMessage()];
        }
    }
    
    public function update_product($product_id, $naam, $omschrijving = null, $maat = null, $afbeelding = null, $prijs = null) {
        $sql = "UPDATE products SET naam = :naam, omschrijving = :omschrijving, maat = :maat, afbeelding = :afbeelding, prijs = :prijs WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':omschrijving', $omschrijving);
            $stmt->bindParam(':maat', $maat);
            $stmt->bindParam(':afbeelding', $afbeelding);
            $stmt->bindParam(':prijs', $prijs, PDO::PARAM_INT);
            $stmt->bindParam(':id', $product_id);
            $stmt->execute();
            return true; 
        } catch (PDOException $e) {
            return ["error" => "Error updating product: " . $e->getMessage()];
        }
    }
    
    public function delete_product($product_id) {
        $sql = "DELETE FROM products WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $product_id);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return ["error" => "Error deleting product: " . $e->getMessage()];
        }
    }
    
    public function format_price($prijs_in_centen) {
        if ($prijs_in_centen === null) return 'Prijs op aanvraag';
        return '€ ' . number_format($prijs_in_centen / 100, 2, ',', '.');
    }
    
    public function validate_product_data($naam, $omschrijving, $maat, $afbeelding, $prijs) {
        $errors = [];
        
        if (empty(trim($naam))) {
            $errors[] = "Naam is verplicht";
        }
        
        if (!empty($maat) && !in_array(strtolower($maat), ['xs', 's', 'm', 'l', 'xl'])) {
            $errors[] = "Maat moet XS, S, M, L of XL zijn";
        }
        
        if (!empty($prijs) && (!is_numeric($prijs) || $prijs < 0)) {
            $errors[] = "Prijs moet een geldig bedrag zijn (in centen)";
        }
        
        return $errors;
    }
}
?>
