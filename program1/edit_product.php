<?php
require_once 'product_crud.php';
require_once 'ownership_manager.php';

$crud = new ProductCrud();
$ownershipManager = new OwnershipManager();
$message = '';
$errors = [];
$product = null;

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$productId) {
    header('Location: index.php');
    exit;
}

$product = $crud->get_product_by_id($productId);

if (!$product) {
    header('Location: index.php');
    exit;
}

if ($_POST) {
    $user_id = trim($_POST['user_id'] ?? '');
    
    if (!$ownershipManager->isOwner($productId, $user_id)) {
        $errors[] = 'Je bent niet gemachtigd om dit product te bewerken.';
    } else {
        $naam = trim($_POST['naam'] ?? '');
        $omschrijving = trim($_POST['omschrijving'] ?? '');
        $maat = trim($_POST['maat'] ?? '');
        $afbeelding = trim($_POST['afbeelding'] ?? '');
        $prijs = trim($_POST['prijs'] ?? '');
        
        $prijs_centen = null;
        if (!empty($prijs)) {
            $prijs_centen = (int)($prijs * 100);
        }
        
        if (empty($afbeelding)) {
            $afbeelding = $product['afbeelding'];
        }
        
        $errors = $crud->validate_product_data($naam, $omschrijving, $maat, $afbeelding, $prijs_centen);
        
        if (empty($errors)) {
            $result = $crud->update_product($productId, $naam, $omschrijving, $maat, $afbeelding, $prijs_centen);
            if ($result === true) {
                $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Product succesvol bijgewerkt!</div>';
                $product = $crud->get_product_by_id($productId);
            } elseif (is_array($result) && isset($result['error'])) {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($result['error']) . '</div>';
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Fout bij het bijwerken van het product.</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Bewerken - Bugimmunity</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="icons/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="icons/bug.png">
    <link rel="icon" type="image/png" sizes="16x16" href="icons/bug.png">
    <link rel="apple-touch-icon" href="icons/bug.png">
    <link rel="icon" type="image/png" sizes="32x32" href="icons/bug.png">
    <link rel="icon" type="image/png" sizes="16x16" href="icons/bug.png">
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

    <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Product Bewerken</h2>
            
            <?php echo $message; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="naam" class="block text-sm font-medium text-gray-700 mb-2">
                        Productnaam <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="naam" name="naam" 
                           value="<?php echo htmlspecialchars($_POST['naam'] ?? $product['naam']); ?>"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="omschrijving" class="block text-sm font-medium text-gray-700 mb-2">
                        Omschrijving
                    </label>
                    <textarea id="omschrijving" name="omschrijving" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($_POST['omschrijving'] ?? $product['omschrijving']); ?></textarea>
                </div>
                
                <div>
                    <label for="maat" class="block text-sm font-medium text-gray-700 mb-2">
                        Maat
                    </label>
                    <select id="maat" name="maat"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecteer maat</option>
                        <?php 
                        $currentMaat = $_POST['maat'] ?? $product['maat'];
                        $maten = ['xs', 's', 'm', 'l', 'xl'];
                        foreach ($maten as $maat): 
                        ?>
                            <option value="<?php echo $maat; ?>" <?php echo ($currentMaat == $maat) ? 'selected' : ''; ?>>
                                <?php echo strtoupper($maat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (!empty($product['afbeelding'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Huidige Afbeelding
                        </label>
                        <div class="w-32 h-32 border rounded-lg overflow-hidden">
                            <img src="<?php echo htmlspecialchars($product['afbeelding']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['naam']); ?>"
                                 class="w-full h-full object-cover">
                        </div>
                    </div>
                <?php endif; ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nieuwe Afbeelding (optioneel)
                    </label>
                    
                    <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors cursor-pointer">
                        <div id="dropContent">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-600 mb-2">Sleep een afbeelding hierheen of klik om te selecteren</p>
                            <p class="text-sm text-gray-500">JPG, PNG, GIF, WebP - Max 5MB</p>
                        </div>
                        <div id="uploadProgress" class="hidden">
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div id="progressBar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                            <p class="text-sm text-gray-600">Uploading...</p>
                        </div>
                        <div id="uploadSuccess" class="hidden">
                            <svg class="w-12 h-12 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-green-600 font-medium">Afbeelding geüpload!</p>
                            <p id="uploadedFileName" class="text-sm text-gray-600 mt-1"></p>
                        </div>
                    </div>
                    
                    <input type="file" id="fileInput" name="image" accept="image/*" class="hidden">
                    
                    <input type="hidden" id="afbeeldingPath" name="afbeelding" value="">
                    
                    <div id="uploadError" class="hidden mt-2 text-red-600 text-sm"></div>
                </div>
                
                <div>
                    <label for="prijs" class="block text-sm font-medium text-gray-700 mb-2">
                        Prijs (in euro's)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">€</span>
                        <input type="number" id="prijs" name="prijs" 
                               value="<?php echo htmlspecialchars($_POST['prijs'] ?? number_format($product['prijs'] / 100, 2, '.', '')); ?>"
                               step="0.01" min="0"
                               placeholder="0.00"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Wijzigingen Opslaan
                    </button>
                    <a href="index.php" 
                       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Annuleren
                    </a>
                </div>
                
                <input type="hidden" id="user_id" name="user_id" value="">
            </form>
        </div>
    </main>

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

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('user_id').value = getUserId();
        });

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const dropContent = document.getElementById('dropContent');
        const uploadProgress = document.getElementById('uploadProgress');
        const uploadSuccess = document.getElementById('uploadSuccess');
        const uploadError = document.getElementById('uploadError');
        const progressBar = document.getElementById('progressBar');
        const uploadedFileName = document.getElementById('uploadedFileName');
        const afbeeldingPath = document.getElementById('afbeeldingPath');

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                uploadFile(file);
            }
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadFile(files[0]);
            }
        });

        function uploadFile(file) {
            dropContent.classList.add('hidden');
            uploadProgress.classList.remove('hidden');
            uploadSuccess.classList.add('hidden');
            uploadError.classList.add('hidden');

            const formData = new FormData();
            formData.append('image', file);

            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                }
            });

            xhr.addEventListener('load', () => {
                uploadProgress.classList.add('hidden');
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            uploadSuccess.classList.remove('hidden');
                            uploadedFileName.textContent = response.filename;
                            afbeeldingPath.value = response.filepath;
                        } else {
                            showError(response.message);
                        }
                    } catch (e) {
                        showError('Er is een fout opgetreden bij het verwerken van de response.');
                    }
                } else {
                    showError('Er is een fout opgetreden bij het uploaden.');
                }
            });

            xhr.addEventListener('error', () => {
                uploadProgress.classList.add('hidden');
                showError('Er is een netwerkfout opgetreden.');
            });

            xhr.open('POST', 'upload_image.php');
            xhr.send(formData);
        }

        function showError(message) {
            dropContent.classList.remove('hidden');
            uploadError.classList.remove('hidden');
            uploadError.textContent = message;
        }
    </script>
</body>
</html>
