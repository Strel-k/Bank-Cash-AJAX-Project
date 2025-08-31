<?php
/**
 * Download Face-API.js AI Models
 * Run this script to automatically download the required AI models
 */

// Create models directory
$modelsDir = __DIR__ . '/public/models';
if (!file_exists($modelsDir)) {
    mkdir($modelsDir, 0755, true);
    echo "✅ Created models directory\n";
}

// AI model files to download
$models = [
    'tiny_face_detector_model-weights_manifest.json' => 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/tiny_face_detector_model-weights_manifest.json',
    'tiny_face_detector_model-shard1' => 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/tiny_face_detector_model-shard1',
    'face_landmark_68_model-weights_manifest.json' => 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1' => 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_landmark_68_model-shard1',
    'face_recognition_model-weights_manifest.json' => 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1' => 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_recognition_model-shard1',
    'face_recognition_model-shard2' => 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_recognition_model-shard2',
    'face_expression_model-weights_manifest.json' => 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_expression_model-weights_manifest.json',
    'face_expression_model-shard1' => 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/face_expression_model-shard1'
];

echo "🤖 Downloading Face-API.js AI Models...\n";
echo "This will enable real facial recognition in your B-Cash app.\n\n";

$downloaded = 0;
$total = count($models);

foreach ($models as $filename => $url) {
    $filepath = $modelsDir . '/' . $filename;
    
    if (file_exists($filepath)) {
        echo "⏭️  Skipping $filename (already exists)\n";
        $downloaded++;
        continue;
    }
    
    echo "📥 Downloading $filename... ";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'user_agent' => 'B-Cash Face Recognition Setup'
        ]
    ]);
    
    $data = file_get_contents($url, false, $context);
    
    if ($data === false) {
        echo "❌ FAILED\n";
        continue;
    }
    
    if (file_put_contents($filepath, $data) !== false) {
        echo "✅ SUCCESS (" . formatBytes(strlen($data)) . ")\n";
        $downloaded++;
    } else {
        echo "❌ FAILED (could not write file)\n";
    }
}

echo "\n";
echo "📊 Download Summary:\n";
echo "✅ Downloaded: $downloaded/$total models\n";

if ($downloaded === $total) {
    echo "\n🎉 SUCCESS! All AI models downloaded successfully!\n";
    echo "\n🚀 Next Steps:\n";
    echo "1. Go to your registration page\n";
    echo "2. Complete the form and reach Step 3 (Face Verification)\n";
    echo "3. Click 'Start Camera' and take a photo\n";
    echo "4. Check browser console for AI verification messages\n";
    echo "\n💡 You should see messages like:\n";
    echo "   ✅ AI Face Verification PASSED!\n";
    echo "   Similarity: 85.2%\n";
    echo "   Liveness: 78.5%\n";
} else {
    echo "\n⚠️  Some models failed to download. Try running this script again.\n";
    echo "Or download them manually from the setup page.\n";
}

function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

echo "\n";
?>