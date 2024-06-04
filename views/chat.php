<?php
require_once '../controllers/ChatController.php';
$controller = new ChatController();

$pengirim = isset($_GET['pengirim']) ? $_GET['pengirim'] : '';
$penerima = isset($_GET['penerima']) ? $_GET['penerima'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pengirim']) && isset($_POST['penerima']) && isset($_POST['pesan'])) {
    $controller->kirimPesan();
    header('Location: ' . $_SERVER['PHP_SELF'] . '?pengirim=' . $pengirim . '&penerima=' . $penerima);
    exit();
}

$pesan = $controller->model->getPesan($pengirim, $penerima);
?>

<script>
    var lastMessageId = 0;
    var pengirim = '<?php echo $pengirim; ?>';
    var penerima = '<?php echo $penerima; ?>';

    function getNewMessages() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_new_messages.php?pengirim=' + pengirim + '&penerima=' + penerima + '&lastId=' + lastMessageId, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                var messages = JSON.parse(xhr.responseText);
                for (var i = 0; i < messages.length; i++) {
                    var message = messages[i];
                    lastMessageId = Math.max(lastMessageId, message.id);
                    addMessageToChat(message.pengirim, message.pesan, message.waktu);
                }
            }
        };
        xhr.send();
    }

    function addMessageToChat(pengirim, pesan, waktu) {
        var chatBody = document.querySelector('.chat-body');
        var pesanElement = document.createElement('div');
        pesanElement.classList.add('pesan');
        pesanElement.innerHTML = `
            <strong>${pengirim}</strong>
            <p>${pesan}</p>
            <small>${waktu}</small>
        `;
        chatBody.appendChild(pesanElement);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    setInterval(getNewMessages, 500); 
</script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .chat-container {
            width: 800px;
            height: 450px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: none;
        }

        .chat-header {
            text-align: center;
            background-color: #f0f0f0;
            padding: 10px;
        }

        .chat-body {
            height: 300px;
            overflow-y: scroll;
            padding: 10px;
        }

        .pesan {
            margin-bottom: 10px;
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
        }

        .chat-footer {
            padding: 10px;
            background-color: #fff;
        }

        .chat-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #007bff;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 9999;
        }
    </style>
</head>


<body>
<div>
    <nav class="navbar navbar-expand-lg navbar-light  " style="box-shadow: rgba(0, 0, 0, 0.300) 0px 0px 20px;">
        <div class="container-fluid">
            <a class="navbar-brand" style="color: #134A85; font-weight: bold;" href="#">FILKOM Chat</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" style="color: #134A85;" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" style="color: #134A85;" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" style="color: #134A85;">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#" style="color: #134A85;">Action</a></li>
                            <li><a class="dropdown-item" href="#" style="color: #134A85;">Another action</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#" style="color: #134A85;">Something else here</a></li>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>
</div>

<div class="text-center mt-5">
    <h1>Selamat Datang di Filkom Chat</h1>
    <h3>Silahkan tekan tombol dibawah untuk memulai</h3>
</div>
    <div class="d-flex justify-content-center align-items-center center vh-100">
        <div class="chat-toggle">
            <i class="bi bi-chat-dots">chat</i>
        </div>
        <div class="chat-container">
            <div class="chat-header">
                <h3>Chat : <?php echo $penerima; ?></h3>
            </div>
            <div class="chat-body">
                <?php foreach ($pesan as $p): ?>
                    <div class="pesan">
                        <strong><?php echo $p['pengirim']; ?></strong>
                        <p><?php echo $p['pesan']; ?></p>
                        <small><?php echo $p['waktu']; ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="chat-footer">
                <form method="post"
                    action="<?php echo $_SERVER['PHP_SELF'] . '?pengirim=' . $pengirim . '&penerima=' . $penerima; ?>">
                    <input type="hidden" name="pengirim" value="<?php echo $pengirim; ?>">
                    <input type="hidden" name="penerima" value="<?php echo $penerima; ?>">
                    <div class="input-group">
                        <input type="text" class="form-control" name="pesan" placeholder="Tulis pesan..." required>
                        <button type="submit" class="btn" style="background-color: #006B89; color: white;">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div>
        <footer style="background-color: #006B89; width: 100%; margin-top: 80px;">
            <div class="container p-4">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-4">
                        <h5 class="mb-3" style="letter-spacing: 2px; color: white">FILKOM Chat</h5>
                        <p style="color: white;">
                            Web FILKOM library adalah sebuah situs web yang dirancang untuk memberikan akses kepada
                            anggota Fakultas Ilmu Komputer (FILKOM) atau masyarakat umum untuk saling berkomunikasi
                        </p>
                    </div>
                </div>
            </div>
            <div class="text-center p-3" style="color: white; background-color: rgba(0, 0, 0, 0.2);">
                © 2024 Copyright: FILKOM Chat
            </div>
            <!-- Copyright -->
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        const chatToggle = document.querySelector('.chat-toggle');
        const chatContainer = document.querySelector('.chat-container');

        chatToggle.addEventListener('click', () => {
            chatContainer.style.display = chatContainer.style.display === 'none' ? 'block' : 'none';
        });
    </script>
</body>

</html>