<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        $sql = "INSERT INTO NguoiDung (ten_dang_nhap, mat_khau, ho_ten, email, vai_tro) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute([$data['ten_dang_nhap'], $data['mat_khau'], $data['ho_ten'], $data['email'], $data['vai_tro']]);
        echo json_encode(["status" => $res ? "success" : "error"]);
        break;

    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        $sql = "UPDATE NguoiDung SET ho_ten=?, email=?, vai_tro=? WHERE id_nguoidung=?";
        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute([$data['ho_ten'], $data['email'], $data['vai_tro'], $data['id_nguoidung']]);
        echo json_encode(["status" => $res ? "success" : "error"]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $pdo->prepare("DELETE FROM NguoiDung WHERE id_nguoidung = ?");
            $res = $stmt->execute([$id]);
            echo json_encode(["status" => "success"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Ràng buộc khóa ngoại"]);
        }
        break;
    // Trong file api_user.php, phần case 'read':
case 'read':
    $search = $_GET['search'] ?? '';
    if ($search !== '') {
        // Tìm kiếm theo Họ tên hoặc Email
        $sql = "SELECT id_nguoidung, ten_dang_nhap, ho_ten, email, vai_tro 
                FROM NguoiDung 
                WHERE ho_ten LIKE ? OR email LIKE ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT id_nguoidung, ten_dang_nhap, ho_ten, email, vai_tro FROM NguoiDung");
    }
    echo json_encode($stmt->fetchAll());
    break;
}
?>
