<?php
require_once 'product_crud.php';
require_once 'ownership_manager.php';

$crud = new ProductCrud();
$ownershipManager = new OwnershipManager();
$product = null;
$error = null;

$product_id = $_GET['id'] ?? null;

if (!$product_id || !is_numeric($product_id)) {
    $error = "Geen geldig product ID opgegeven.";
} else {
    $product = $crud->get_product_by_id($product_id);
    if (!$product || (is_array($product) && isset($product['error']))) {
        $error = "Product niet gevonden.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['naam']) : 'Product niet gevonden'; ?> - Bugimmunity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="icons/bug.png">
    <link rel="icon" type="image/png" sizes="16x16" href="icons/bug.png">
    <link rel="apple-touch-icon" href="icons/bug.png">
    <link rel="apple-touch-icon" href="icons/bug.png">
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <img src="icons/bug.png" alt="Bugimmunity" class="w-10 h-10 mr-3">
                    Bugimmunity
                </h1>
                <nav class="space-x-4">
                    <a href="index.php" class="text-blue-600 hover:text-blue-800 font-medium">Alle Producten</a>
                    <a href="add_product.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Product Toevoegen</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="index.php" class="hover:text-blue-600">Alle Producten</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900"><?php echo $product ? htmlspecialchars($product['naam']) : 'Product details'; ?></li>
            </ol>
        </nav>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <a href="index.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Terug naar overzicht
            </a>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="md:flex">
                    <div class="md:w-1/2">
                        <div class="aspect-w-1 aspect-h-1 w-full h-96 bg-gray-200">
                            <?php if (!empty($product['afbeelding'])): ?>
                                <img src="<?php echo htmlspecialchars($product['afbeelding']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['naam']); ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="md:w-1/2 p-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">
                            <?php echo htmlspecialchars($product['naam']); ?>
                        </h1>
                        
                        <div class="text-3xl font-bold text-blue-600 mb-6">
                            <?php echo $crud->format_price($product['prijs']); ?>
                        </div>
                        
                        <?php if (!empty($product['maat'])): ?>
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Maat</h3>
                                <span class="inline-block bg-gray-100 text-gray-800 text-sm px-3 py-1 rounded uppercase font-medium">
                                    <?php echo htmlspecialchars($product['maat']); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($product['omschrijving'])): ?>
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Omschrijving</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($product['omschrijving'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="border-t pt-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Product Informatie</h3>
                            <dl class="space-y-2">
                                <?php if (!empty($product['maat'])): ?>
                                <div class="flex">
                                    <dt class="text-sm text-gray-500 w-24">Maat:</dt>
                                    <dd class="text-sm text-gray-900 uppercase"><?php echo htmlspecialchars($product['maat']); ?></dd>
                                </div>
                                <?php endif; ?>
                            </dl>
                        </div>
                        
                        <div class="mt-8 space-y-3">
                            <button id="addToCartBtn" onclick="addToCart()" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                In Winkelwagen
                            </button>
                            <a href="index.php" 
                               class="w-full bg-gray-200 text-gray-800 py-3 px-6 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-center block">
                                Terug naar overzicht
                            </a>
                            
                            <div id="ownerActions" class="hidden space-y-2 pt-4 border-t">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Jouw product beheren</h4>
                                <div class="flex gap-2">
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                       class="flex-1 bg-green-600 text-white text-center py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                        Bewerken
                                    </a>
                                    <button onclick="deleteProduct(<?php echo $product['id']; ?>)" 
                                            class="flex-1 bg-red-600 text-white text-center py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                                        Verwijderen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-500">© 2025 Bugimmunity. Alle rechten voorbehouden.</p>
        </div>
    </footer>

    <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="modalTitle">Product Verwijderen</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="modalMessage">
                        Weet je zeker dat je dit product wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.
                    </p>
                </div>
                <div class="flex gap-3 mt-4">
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
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" id="alertIcon">
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="alertTitle">Melding</h3>
                <div class="mt-2 px-7 py-3">
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
            <?php if ($product): ?>
            const userId = getUserId();
            const productId = <?php echo $product['id']; ?>;
            const owner = '<?php echo $ownershipManager->getOwner($product['id']) ?? ''; ?>';

            if (owner === userId) {
                document.getElementById('ownerActions').classList.remove('hidden');
                document.getElementById('addToCartBtn').style.display = 'none';
            }
            <?php endif; ?>
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

                    const data = await response.json();

                    if (data.success) {
                        await showAlert('Product succesvol verwijderd!', 'success');
                        window.location.href = 'index.php';
                    } else {
                        await showAlert('Fout bij het verwijderen: ' + data.message, 'error');
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
</body>
</html>
