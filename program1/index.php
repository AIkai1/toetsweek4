<?php
require_once 'product_crud.php';
require_once 'ownership_manager.php';

$crud = new ProductCrud();
$ownershipManager = new OwnershipManager();
$products = $crud->get_all_products();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bugimmunity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="icons/favicon.ico?v=2025" type="image/x-icon">
    <link rel="shortcut icon" href="icons/favicon.ico?v=2025" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="icons/bug.png?v=2025">
    <link rel="icon" type="image/png" sizes="16x16" href="icons/bug.png?v=2025">
    <link rel="apple-touch-icon" href="icons/bug.png?v=2025">
    <style>
        @media (max-width: 640px) {
            .mobile-padding { padding-left: 1rem; padding-right: 1rem; }
        }
        .product-image {
            aspect-ratio: 1 / 1;
            min-height: 200px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto mobile-padding px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-4 sm:py-6 space-y-3 sm:space-y-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center justify-center sm:justify-start">
                    <img src="icons/bug.png" alt="Bugimmunity" class="w-6 h-6 sm:w-8 sm:h-8 mr-2 sm:mr-3">
                    Bugimmunity
                </h1>
                <nav class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 items-center">
                    <a href="index.php" class="text-blue-600 hover:text-blue-800 font-medium px-3 py-1 rounded-md hover:bg-blue-50 transition-colors">Alle Producten</a>
                    <a href="add_product.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center transition-colors">Product Toevoegen</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto mobile-padding px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="mb-6 sm:mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 text-center sm:text-left">Alle Producten</h2>
            <p class="text-gray-600 text-center sm:text-left">Ontdek onze collectie kleding</p>
        </div>

        <?php if (empty($products) || (is_array($products) && isset($products['error']))): ?>
            <div class="text-center py-12">
                <div class="text-gray-500 text-lg">Geen producten gevonden.</div>
                <a href="add_product.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Voeg het eerste product toe
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow" data-product-id="<?php echo $product['id']; ?>">
                        <div class="product-image w-full bg-gray-200">
                            <?php if (!empty($product['afbeelding'])): ?>
                                <img src="<?php echo htmlspecialchars($product['afbeelding']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['naam']); ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 sm:w-16 sm:h-16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-3 sm:p-4">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($product['naam']); ?>
                            </h3>
                            
                            <?php if (!empty($product['omschrijving'])): ?>
                                <p class="text-gray-600 text-xs sm:text-sm mb-3 line-clamp-2">
                                    <?php echo htmlspecialchars(substr($product['omschrijving'], 0, 80)); ?>
                                    <?php if (strlen($product['omschrijving']) > 80): ?>...<?php endif; ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <?php if (!empty($product['maat'])): ?>
                                        <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded uppercase">
                                            <?php echo htmlspecialchars($product['maat']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-base sm:text-lg font-bold text-blue-600">
                                        <?php echo $crud->format_price($product['prijs']); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" 
                                   class="w-full bg-blue-600 text-white text-center py-2 px-3 sm:px-4 rounded-lg hover:bg-blue-700 transition-colors block text-sm sm:text-base">
                                    Bekijk Details
                                </a>
                                
                                <button onclick="addToCart()" class="cart-btn w-full bg-green-600 text-white text-center py-2 px-3 sm:px-4 rounded-lg hover:bg-green-700 transition-colors text-xs sm:text-sm">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0h15M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"/>
                                    </svg>
                                    In Winkelwagen
                                </button>
                                
                                <div class="owner-actions hidden flex gap-2">
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                       class="flex-1 bg-amber-600 text-white text-center py-2 px-2 sm:px-4 rounded-lg hover:bg-amber-700 transition-colors text-xs sm:text-sm">
                                        Bewerken
                                    </a>
                                    <button onclick="deleteProduct(<?php echo $product['id']; ?>)" 
                                            class="flex-1 bg-red-600 text-white text-center py-2 px-2 sm:px-4 rounded-lg hover:bg-red-700 transition-colors text-xs sm:text-sm">
                                        Verwijderen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-white border-t mt-8 sm:mt-12">
        <div class="max-w-7xl mx-auto mobile-padding px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
            <p class="text-center text-gray-500 text-sm sm:text-base">© 2025 Bugimmunity. Alle rechten voorbehouden.</p>
        </div>
    </footer>

    <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="modalTitle">Product Verwijderen</h3>
                <div class="mt-2 px-3 sm:px-7 py-3">
                    <p class="text-sm text-gray-500" id="modalMessage">
                        Weet je zeker dat je dit product wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 mt-4">
                    <button id="modalCancel" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Annuleren
                    </button>
                    <button id="modalConfirm" class="flex-1 px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Verwijderen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="alertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" id="alertIcon">
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="alertTitle">Melding</h3>
                <div class="mt-2 px-3 sm:px-7 py-3">
                    <p class="text-sm text-gray-500" id="alertMessage">
                    </p>
                </div>
                <div class="mt-4">
                    <button id="alertOk" class="w-full px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generateUserId() {
            return 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        function getUserId() {
            let userId = localStorage.getItem('bugimmunity_user_id');
            if (!userId) {
                userId = generateUserId();
                localStorage.setItem('bugimmunity_user_id', userId);
            }
            return userId;
        }
        function showAlert(message, type = 'info', title = 'Melding') {
            const modal = document.getElementById('alertModal');
            const alertTitle = document.getElementById('alertTitle');
            const alertMessage = document.getElementById('alertMessage');
            const alertIcon = document.getElementById('alertIcon');
            const alertOk = document.getElementById('alertOk');

            alertTitle.textContent = title;
            alertMessage.textContent = message;

            if (type === 'error') {
                alertIcon.innerHTML = `
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                `;
                alertOk.className = "w-full px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500";
            } else if (type === 'success') {
                alertIcon.innerHTML = `
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                `;
                alertOk.className = "w-full px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500";
            } else {
                alertIcon.innerHTML = `
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                `;
                alertOk.className = "w-full px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500";
            }

            modal.classList.remove('hidden');
            alertOk.focus();

            return new Promise((resolve) => {
                alertOk.onclick = () => {
                    modal.classList.add('hidden');
                    resolve(true);
                };
            });
        }

        function showConfirm(message, title = 'Bevestiging') {
            const modal = document.getElementById('confirmModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalConfirm = document.getElementById('modalConfirm');
            const modalCancel = document.getElementById('modalCancel');

            modalTitle.textContent = title;
            modalMessage.textContent = message;
            modal.classList.remove('hidden');
            modalCancel.focus();

            return new Promise((resolve) => {
                modalConfirm.onclick = () => {
                    modal.classList.add('hidden');
                    resolve(true);
                };
                modalCancel.onclick = () => {
                    modal.classList.add('hidden');
                    resolve(false);
                };
            });
        }

        function checkOwnership() {
            const userId = getUserId();
            const products = <?php echo json_encode(array_map(function($p) { return $p['id']; }, $products ?: [])); ?>;
            const ownership = <?php 
                $ownershipData = [];
                if ($products) {
                    foreach ($products as $product) {
                        $owner = $ownershipManager->getOwner($product['id']);
                        if ($owner) {
                            $ownershipData[$product['id']] = $owner;
                        }
                    }
                }
                echo json_encode($ownershipData);
            ?>;

            products.forEach(productId => {
                const productCard = document.querySelector(`[data-product-id="${productId}"]`);
                const ownerActions = productCard.querySelector('.owner-actions');
                const cartBtn = productCard.querySelector('.cart-btn');
                
                if (ownership[productId] === userId) {
                    ownerActions.classList.remove('hidden');
                    cartBtn.style.display = 'none';
                }
            });
        }
        async function addToCart() {
            await showAlert('Deze functie is nog niet beschikbaar. Kom later terug!', 'info', 'Winkelwagen');
        }

        async function deleteProduct(productId) {
            const confirmed = await showConfirm(
                'Weet je zeker dat je dit product wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.',
                'Product Verwijderen'
            );
            
            if (confirmed) {
                const userId = getUserId();
                console.log('Deleting product:', { productId, userId });
                
                try {
                    const response = await fetch('delete_product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            user_id: userId
                        })
                    });

                    console.log('Response status:', response.status);
                    const data = await response.json();
                    console.log('Response data:', data);

                    if (data.success) {
                        await showAlert('Product succesvol verwijderd!', 'success');
                        location.reload();
                    } else {
                        await showAlert('Fout bij het verwijderen: ' + data.message, 'error');
                        if (data.debug) {
                            console.log('Debug info:', data.debug);
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    await showAlert('Er is een netwerkfout opgetreden.', 'error');
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            checkOwnership();
        });
    </script>
