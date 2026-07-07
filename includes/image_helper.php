<?php
function getProductImage($name, $category_name = '', $image_path = null) {
    $images = getProductImages($name, $category_name, $image_path);
    return !empty($images) ? $images[0] : 'assets/images/products/hoodie.jpg';
}

function getProductImages($name, $category_name = '', $image_path = null, $gallery_paths = null) {
    $images = [];
    $base = 'assets/images/products/';

    // Admin-uploaded local file (relative path)
    if (!empty($image_path) && !str_starts_with($image_path, 'http')) {
        if (file_exists(__DIR__ . '/../' . $image_path)) {
            $images[] = $image_path;
        }
        if (!empty($gallery_paths)) {
            foreach ((array) json_decode($gallery_paths, true) as $gp) {
                if (!empty($gp) && file_exists(__DIR__ . '/../' . $gp)) {
                    $images[] = $gp;
                }
            }
        }
        if (!empty($images)) return $images;
    }

    // External URL stored in DB (legacy / admin-entered URL)
    if (!empty($image_path) && str_starts_with($image_path, 'http')) {
        return [$image_path];
    }

    // Gallery images only
    if (!empty($gallery_paths)) {
        foreach ((array) json_decode($gallery_paths, true) as $gp) {
            if (!empty($gp) && file_exists(__DIR__ . '/../' . $gp)) {
                $images[] = $gp;
            }
        }
        if (!empty($images)) return $images;
    }

    // Named product overrides (multi-angle images)
    $nameLower = strtolower($name);
    if (str_contains($nameLower, 'nsbm hoodie')) {
        return [$base.'hoodie_front.png', $base.'hoodie_back.png', $base.'hoodie_side.png', $base.'hoodie_folded.png'];
    }
    if (str_contains($nameLower, 'white t-shirt') || str_contains($nameLower, 'polo shirt')) {
        return [$base.'tshirt_front.png', $base.'tshirt_back.png', $base.'tshirt_folded.png'];
    }
    if (str_contains($nameLower, 'backpack'))      return [$base.'backpack_front.jpg'];
    if (str_contains($nameLower, 'wireless mouse') || str_contains($nameLower, 'gaming mouse')) return [$base.'mouse_top.jpg'];
    if (str_contains($nameLower, 'notebook'))      return [$base.'notebook_closed.jpg'];

    // Category fallback — all local images
    $catLower = strtolower($category_name);
    $fallbacks = [
        'apparel'     => $base.'hoodie.jpg',
        'stationery'  => $base.'notebook.jpg',
        'tech'        => $base.'mouse.jpg',
        'accessories' => $base.'mug.jpg',
        'food'        => $base.'brownies.jpg',
    ];
    foreach ($fallbacks as $cat => $img) {
        if (str_contains($catLower, $cat)) return [$img];
    }

    return [$base.'hoodie.jpg'];
}
?>
