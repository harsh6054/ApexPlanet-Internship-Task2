<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "blog");
if ($conn->connect_error) {
    die("Database connection failed");
}

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $conn->query("INSERT INTO posts (title, content) VALUES ('$title','$content')");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM posts WHERE id=$id");
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $conn->query("UPDATE posts SET title='$title', content='$content' WHERE id=$id");
}

$editPost = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editPost = $conn->query("SELECT * FROM posts WHERE id=$id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Page</title>
    <link rel="stylesheet" href="post.css">
</head>
<body>

<div class="container">

    <div class="top">
        <h2>Welcome, <?php echo $_SESSION['user']; ?></h2>
        <a class="logout" href="post.php?logout=1">Logout</a>
    </div>

    <h2><?php echo $editPost ? "Edit Post" : "Add Post"; ?></h2>

    <form method="post">
        <?php if ($editPost) { ?>
            <input type="hidden" name="id" value="<?php echo $editPost['id']; ?>">
        <?php } ?>

        <input type="text" name="title" placeholder="Post Title"
               value="<?php echo $editPost['title'] ?? ''; ?>" required>

        <textarea name="content" placeholder="Post Content" required><?php
            echo $editPost['content'] ?? '';
        ?></textarea>

        <button name="<?php echo $editPost ? 'update' : 'add'; ?>">
            <?php echo $editPost ? 'Update Post' : 'Add Post'; ?>
        </button>
    </form>

    <hr>


    <?php
    $result = $conn->query("SELECT * FROM posts ORDER BY id DESC");
    while ($row = $result->fetch_assoc()) {
    ?>
    <div class="post">
        <h3><?php echo $row['title']; ?></h3>
        <p><?php echo $row['content']; ?></p>

        <div class="actions">
            <a href="?edit=<?php echo $row['id']; ?>">Edit</a>
            <a href="?delete=<?php echo $row['id']; ?>"
               onclick="return confirm('Delete this post?')">Delete</a>
        </div>
    </div>
    <?php } ?>

</div>

</body>
</html>
