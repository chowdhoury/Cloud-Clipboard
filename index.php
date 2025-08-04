<?php
function generateCode($length = 5) {
    return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function cleanExpiredEntries($directory, $expiryTime) {
    foreach (glob($directory . "*") as $file) {
        if (filemtime($file) < time() - $expiryTime) {
            unlink($file);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [];

    // Handle text submission
    if (isset($_POST['text'])) {
        $text = $_POST['text'];
        $uploadText = "uploads_text/";
        if (!is_dir($uploadText)) mkdir($uploadText, 0777, true);

        $code = generateCode();
        $textFile = $uploadText . $code . ".txt";
        file_put_contents($textFile, $text);

        $response['code'] = $code;
        $response['type'] = 'text';
    }

    // Handle file submission
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadImage = "uploads_image/";
        if (!is_dir($uploadImage)) mkdir($uploadImage, 0777, true);

        $code = generateCode();
        $fileName = $code . "_" . basename($_FILES['image']['name']);
        $imagePath = $uploadImage . $fileName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);

        $response['code'] = $code;
        $response['type'] = 'file';
    }

    // Clean expired entries (24 hours = 86400 seconds)
    cleanExpiredEntries("uploads_text/", 86400);
    cleanExpiredEntries("uploads_image/", 86400);

    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
    $code = $_GET['code'];
    $textFile = "uploads_text/" . $code . ".txt";
    $imageFiles = glob("uploads_image/" . $code . "_*");

    if (file_exists($textFile)) {
        // Correctly retrieve and display text content
        header('Content-Type: text/plain');
        echo file_get_contents($textFile);
    } elseif (!empty($imageFiles)) {
        $imagePath = $imageFiles[0];
        echo "<img src='$imagePath' alt='Uploaded Image' style='max-width:100%;'>";
    } else {
        echo "No content found for the provided code.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Text or File</title>
    <link rel="stylesheet" href="style.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const textArea = document.querySelector('textarea[name="text"]');
            const fileInput = document.querySelector('input[name="image"]');
            const form = document.querySelector('form');
            const resultDiv = document.querySelector('#result');

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(form);

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.code) {
                        resultDiv.innerHTML = `Your code is: <strong>${data.code}</strong>. Use this code to retrieve your content.`;
                    }
                });
            });
        });
    </script>
</head>
<body>
    <h1>Store Text or File</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <textarea name="text" cols="30" rows="10" placeholder="Enter your text"></textarea><br>
        <input type="file" name="image"><br>
        <button type="submit">Submit</button>
    </form>
    <div id="result"></div>
    <h2>Retrieve Content</h2>
    <form action="" method="GET">
        <input type="text" name="code" placeholder="Enter your code">
        <button type="submit">Retrieve</button>
    </form>
</body>
</html>