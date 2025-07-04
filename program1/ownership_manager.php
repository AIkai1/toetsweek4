<?php

class OwnershipManager {
    private $ownershipFile = 'ownership.json';
    
    public function __construct() {
        if (!file_exists($this->ownershipFile)) {
            file_put_contents($this->ownershipFile, json_encode([]));
        }
    }
    
    public function addOwnership($productId, $userId) {
        $ownership = $this->getOwnership();
        $ownership[$productId] = $userId;
        return file_put_contents($this->ownershipFile, json_encode($ownership, JSON_PRETTY_PRINT));
    }
    
    public function removeOwnership($productId) {
        $ownership = $this->getOwnership();
        if (isset($ownership[$productId])) {
            unset($ownership[$productId]);
            return file_put_contents($this->ownershipFile, json_encode($ownership, JSON_PRETTY_PRINT));
        }
        return false;
    }
    
    public function getOwner($productId) {
        $ownership = $this->getOwnership();
        return isset($ownership[$productId]) ? $ownership[$productId] : null;
    }
    
    public function isOwner($productId, $userId) {
        return $this->getOwner($productId) === $userId;
    }
    
    public function getUserProducts($userId) {
        $ownership = $this->getOwnership();
        $userProducts = [];
        foreach ($ownership as $productId => $ownerId) {
            if ($ownerId === $userId) {
                $userProducts[] = $productId;
            }
        }
        return $userProducts;
    }
    
    private function getOwnership() {
        if (!file_exists($this->ownershipFile)) {
            return [];
        }
        $content = file_get_contents($this->ownershipFile);
        return json_decode($content, true) ?: [];
    }
}

?>
