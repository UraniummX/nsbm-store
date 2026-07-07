<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once '../config/db.php';

$error = "";
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $category_id = $_POST['category_id'];
    $description = trim($_POST['description']);
    $stock_quantity = isset($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : 10;
    
    $image_path = null;
    $gallery_paths = [];
    
    if (isset($_FILES['product_images'])) {
        $upload_dir = '../assets/images/products/';
        $file_count = count($_FILES['product_images']['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['product_images']['error'][$i] == 0) {
                $filename = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['product_images']['name'][$i]));
                $target_file = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['product_images']['tmp_name'][$i], $target_file)) {
                    $saved_path = 'assets/images/products/' . $filename;
                    
                    if ($i === 0) {
                        $image_path = $saved_path; // First image is main
                    } else {
                        $gallery_paths[] = $saved_path; // Rest are gallery
                    }
                }
            }
        }
    }
    
    $gallery_json = !empty($gallery_paths) ? json_encode($gallery_paths) : null;

    if (!empty($name) && !empty($price)) {
        $sql = "INSERT INTO products (name, price, category_id, description, stock_quantity, image_path, gallery_paths) VALUES (:name, :price, :category_id, :description, :stock_quantity, :image_path, :gallery_paths)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':stock_quantity', $stock_quantity);
        $stmt->bindParam(':image_path', $image_path);
        $stmt->bindParam(':gallery_paths', $gallery_json);
        
        if ($stmt->execute()) {
            header("location: dashboard.php");
            exit;
        } else {
            $error = "Error adding product.";
        }
    } else {
        $error = "Name and Price are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - NSBM Market</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=2">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; }
        .form-container { 
            width: 700px; 
            margin: 50px auto; 
            padding: 40px; 
            background: #fff; 
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        }
        .form-header { text-align: center; margin-bottom: 30px; }
        .form-header h2 { color: #006837; margin: 0; font-size: 2rem; font-weight: 900; }
        .form-header p { color: #888; margin-top: 5px; }
        
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 10px; color: #333; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input[type="text"], .form-group input[type="number"], .form-group textarea { 
            width: 100%; padding: 15px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box; 
            font-size: 1rem; transition: 0.3s; background: #f9f9f9; font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus { border-color: #006837; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(0,104,55,0.1); }
        .form-group textarea { height: 120px; resize: vertical; }
        
        /* Category Tiles */
        .category-tiles { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px; }
        .category-tile { 
            background: #f9f9f9; border: 2px solid #eee; border-radius: 8px; padding: 15px 10px; 
            text-align: center; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
            font-weight: 600; font-size: 0.9rem; color: #555; display: flex; flex-direction: column; align-items: center; gap: 8px;
        }
        .category-tile:hover { border-color: #ccc; background: #f0f0f0; transform: translateY(-2px); }
        .category-tile.active { border-color: #006837; background: rgba(0,104,55,0.05); color: #006837; box-shadow: 0 4px 15px rgba(0,104,55,0.15); transform: translateY(-2px); }
        .category-tile i { font-size: 1.5rem; opacity: 0.7; }
        .category-tile.active i { opacity: 1; color: #006837; }
        
        /* Image Upload Box */
        .upload-box { 
            border: 2px dashed #ccc; border-radius: 12px; padding: 40px 20px; text-align: center; 
            background: #fafafa; cursor: pointer; transition: 0.3s; position: relative;
        }
        .upload-box:hover, .upload-box.dragover { border-color: #006837; background: #f4fbf7; }
        .upload-box input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .upload-box .preview-img { max-height: 200px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: none; margin: 15px auto 0; object-fit: contain; }
        .upload-text { color: #666; font-weight: 600; margin-top: 15px; font-size: 1.1rem; }
        .upload-subtext { color: #aaa; font-size: 0.85rem; margin-top: 5px; }
        .upload-icon { font-size: 3rem; color: #ccc; transition: 0.3s; }
        .upload-box:hover .upload-icon { color: #006837; transform: scale(1.1); }
        
        .row { display: flex; gap: 20px; }
        .col { flex: 1; }
        
        /* Buttons */
        .btn-group { display: flex; gap: 15px; margin-top: 40px; }
        .btn-save { flex: 2; background: linear-gradient(135deg, #006837, #004d28); color: white; border: none; padding: 18px; border-radius: 8px; font-weight: 900; font-size: 1.1rem; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,104,55,0.3); text-transform: uppercase; letter-spacing: 1px; display: flex; justify-content: center; align-items: center; gap: 10px; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,104,55,0.4); }
        .btn-cancel { flex: 1; background: #f0f0f0; color: #555; text-decoration: none; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: bold; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; }
        .btn-cancel:hover { background: #e0e0e0; color: #333; }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h2>Add New Product</h2>
            <p>Create a new item in your store inventory.</p>
        </div>
        
        <?php if($error) echo "<div style='background:#fee;color:#c00;padding:15px;border-radius:8px;margin-bottom:25px;text-align:center;font-weight:bold;'><i class='fa-solid fa-triangle-exclamation'></i> $error</div>"; ?>
        
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" placeholder="e.g. NSBM Premium Hoodie" required>
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <input type="hidden" name="category_id" id="category_id" value="" required>
                <div class="category-tiles">
                    <?php 
                    // Map some icons for visual flair based on common names
                    $icons = [
                        'Apparel' => 'fa-shirt',
                        'Stationery' => 'fa-pen-ruler',
                        'Accessories' => 'fa-glasses',
                        'Food & Snacks' => 'fa-burger',
                        'Tech & Gadgets' => 'fa-laptop'
                    ];
                    foreach($categories as $c): 
                        $icon = isset($icons[$c['name']]) ? $icons[$c['name']] : 'fa-tag';
                    ?>
                        <div class="category-tile" data-id="<?php echo $c['id']; ?>">
                            <i class="fa-solid <?php echo $icon; ?>"></i>
                            <?php echo $c['name']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col form-group">
                    <label>Price (LKR)</label>
                    <input type="number" step="0.01" name="price" placeholder="0.00" required>
                </div>
                <div class="col form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" value="10" min="0" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Product Images</label>
                <div class="upload-box" id="upload-box">
                    <input type="file" name="product_images[]" accept="image/*" id="file-input" multiple>
                    <i class="fa-solid fa-cloud-arrow-up upload-icon" id="upload-icon"></i>
                    <div class="upload-text" id="upload-text">Drag & Drop or Click to Upload Images</div>
                    <div class="upload-subtext" id="upload-subtext">1st image becomes main. Following images become gallery POVs.</div>
                    <div class="preview-container" id="preview-container" style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px; justify-content: center; align-items: flex-end;"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Write a compelling description for this product..."></textarea>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn-save"><i class="fa-solid fa-check"></i> Save Product</button>
                <a href="dashboard.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        // Category Tile Selection Logic
        const tiles = document.querySelectorAll('.category-tile');
        const categoryInput = document.getElementById('category_id');

        tiles.forEach(tile => {
            tile.addEventListener('click', () => {
                // Remove active class from all
                tiles.forEach(t => t.classList.remove('active'));
                // Add active to clicked
                tile.classList.add('active');
                // Set hidden input value
                categoryInput.value = tile.dataset.id;
            });
        });

        // Image Upload Preview Logic
        const fileInput = document.getElementById('file-input');
        const previewContainer = document.getElementById('preview-container');
        const uploadIcon = document.getElementById('upload-icon');
        const uploadText = document.getElementById('upload-text');
        const uploadSubtext = document.getElementById('upload-subtext');
        const uploadBox = document.getElementById('upload-box');

        let selectedFiles = [];

        function updatePreview() {
            previewContainer.innerHTML = '';
            
            if (selectedFiles.length > 0) {
                uploadIcon.style.display = 'none';
                uploadText.innerText = selectedFiles.length + ' image(s) selected';
                uploadSubtext.style.display = 'none';
                uploadBox.style.padding = '20px';
                
                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.height = index === 0 ? '160px' : '100px';
                        img.style.borderRadius = '8px';
                        img.style.boxShadow = '0 4px 10px rgba(0,0,0,0.1)';
                        img.style.objectFit = 'contain';
                        
                        const wrapper = document.createElement('div');
                        wrapper.style.position = 'relative';
                        wrapper.appendChild(img);
                        
                        // Add remove button
                        const removeBtn = document.createElement('span');
                        removeBtn.innerHTML = '&times;';
                        removeBtn.style.position = 'absolute';
                        removeBtn.style.top = '-10px';
                        removeBtn.style.right = '-10px';
                        removeBtn.style.background = '#ff4757';
                        removeBtn.style.color = 'white';
                        removeBtn.style.width = '24px';
                        removeBtn.style.height = '24px';
                        removeBtn.style.borderRadius = '50%';
                        removeBtn.style.textAlign = 'center';
                        removeBtn.style.lineHeight = '22px';
                        removeBtn.style.cursor = 'pointer';
                        removeBtn.style.fontWeight = 'bold';
                        removeBtn.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
                        removeBtn.onclick = function(ev) {
                            ev.preventDefault();
                            ev.stopPropagation();
                            selectedFiles.splice(index, 1);
                            updatePreview();
                        };
                        wrapper.appendChild(removeBtn);
                        
                        if (index === 0) {
                            const badge = document.createElement('span');
                            badge.innerText = 'MAIN';
                            badge.style.position = 'absolute';
                            badge.style.top = '-10px';
                            badge.style.left = '-10px';
                            badge.style.background = '#006837';
                            badge.style.color = 'white';
                            badge.style.padding = '4px 10px';
                            badge.style.borderRadius = '12px';
                            badge.style.fontSize = '0.7rem';
                            badge.style.fontWeight = 'bold';
                            badge.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
                            wrapper.appendChild(badge);
                        }
                        
                        previewContainer.appendChild(wrapper);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                uploadIcon.style.display = 'block';
                uploadText.innerText = 'Drag & Drop or Click to Upload Images';
                uploadSubtext.style.display = 'block';
                uploadBox.style.padding = '40px 20px';
            }
            
            // Sync to actual input
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            fileInput.files = dt.files;
        }

        function handleFiles(files) {
            if (files && files.length > 0) {
                Array.from(files).forEach(file => {
                    selectedFiles.push(file);
                });
                updatePreview();
            }
        }

        fileInput.addEventListener('change', function(e) {
            if (this.files) {
                handleFiles(this.files);
            }
        });

        // Drag and Drop Logic
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadBox.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadBox.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadBox.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            uploadBox.classList.add('dragover');
        }

        function unhighlight(e) {
            uploadBox.classList.remove('dragover');
        }

        uploadBox.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            let dt = e.dataTransfer;
            let files = dt.files;
            
            if (files && files.length > 0) {
                fileInput.files = files; // Assign files to input
                handleFiles(files); // Trigger preview
            }
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if(!categoryInput.value) {
                e.preventDefault();
                alert('Please select a category by clicking one of the tiles.');
            }
        });
    </script>
</body>
</html>
