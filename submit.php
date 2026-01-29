<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Ho_Chi_Minh');

$dir = __DIR__ . '/HoSo';
if (!is_dir($dir)) mkdir($dir, 0777, true);

function clean($s){
    return trim(strip_tags($s ?? ''));
}

$hoten = clean($_POST['hoten'] ?? '');
$tuoi  = clean($_POST['tuoi'] ?? '');
$lop   = clean($_POST['lop'] ?? '');
$club  = clean($_POST['club'] ?? '');
$sdt   = clean($_POST['sdt'] ?? '');

$files = glob($dir . '/hs_*.txt');
$max = 0;
foreach ($files as $f){
    if (preg_match('/hs_(\d+)\.txt$/', $f, $m)) {
        $max = max($max, intval($m[1]));
    }
}
$id = $max + 1;
$seq = str_pad($id, 4, '0', STR_PAD_LEFT);
$timestamp = date('Y-m-d H:i:s');

$original = 'Không có';
if (!empty($_FILES['ho_so_file']['name']) && $_FILES['ho_so_file']['error'] === UPLOAD_ERR_OK){
    $original = basename($_FILES['ho_so_file']['name']);
    $tmp = $_FILES['ho_so_file']['tmp_name'];

    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','pdf'];
    if (in_array($ext, $allow)) {
        $safe = $dir.'/tmp_'.$seq.'.'.$ext;
        if (move_uploaded_file($tmp, $safe)) {
            unlink($safe); // xóa ngay theo yêu cầu
        }
    }
}

$summary =
"ID: $seq
Thời gian: $timestamp
Họ tên: $hoten
Tuổi: $tuoi
Lớp: $lop
SĐT: $sdt
CLB: $club
Tệp nộp: $original
";

file_put_contents($dir."/hs_$seq.txt", $summary, LOCK_EX);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Đã gửi hồ sơ</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
body{
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color:white;
    font-family:Poppins,sans-serif;
    padding:50px;
}
.box{
    background:rgba(255,255,255,0.08);
    padding:40px;
    border-radius:20px;
    max-width:600px;
    margin:auto;
    text-align:center;
}
a{color:#00ffcc;text-decoration:none;font-weight:bold;}
</style>
</head>

<body>
<div class="box">
<h1>Đăng ký thành công</h1>
<p>Mã hồ sơ: <b><?php echo $seq ?></b></p>
<p>Họ tên: <?php echo htmlspecialchars($hoten) ?></p>
<p>CLB: <?php echo htmlspecialchars($club) ?></p>
<p>Thời gian: <?php echo $timestamp ?></p>
<br>
<a href="index.html">⬅ Quay về trang chính</a>
</div>
</body>
</html>
