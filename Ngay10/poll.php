<?php
include 'includes/db.php';
header('Content-Type: application/json');
if (isset($_POST['option'])) {
    $option = $_POST['option'];
    $stmt = $pdo->prepare("UPDATE polls SET vote_count = vote_count + 1 WHERE option_name = ?");
    $stmt->execute([$option]);
    
    $stmt = $pdo->query("SELECT option_name, vote_count FROM polls");
    $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = array_sum(array_column($polls, 'vote_count'));
    $result = [];
    foreach ($polls as $poll) {
        $percent = $total ? round(($poll['vote_count'] / $total) * 100, 2) : 0;
        $result[] = ['option' => $poll['option_name'], 'percent' => $percent];
    }
    echo json_encode(['success' => true, 'result' => $result]);
} else {
    echo json_encode(['success' => false]);
}
?>