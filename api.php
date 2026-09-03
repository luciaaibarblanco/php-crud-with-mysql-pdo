<?php

declare(strict_types=1);

/*
CREATE DATABASE IF NOT EXISTS `biblioteca_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `libros` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `titulo` VARCHAR(100) NOT NULL,
    `autor` VARCHAR(100) NOT NULL,
    `precio` DECIMAL(8, 2) NOT NULL
);

INSERT INTO `libros` (`titulo`, `autor`, `precio`) VALUES
('Asado Argentino', 'Lucía Aibar Blanco', 120.30),
('El Precio Justo', 'Lita', 49.90),
('PHP 123', 'Michael Jordan', 22.33);
*/

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

class Database
{
    private string $host    = '';
    private string $db      = '';
    private string $user    = '';
    private string $pass    = '';
    private string $charset = '';

    private PDO|null $_pdo = null;
    public PDO|null $pdo {
        get => $this->_pdo;
    }

    public function conectar(): void
    {
        $this->host    = getenv('MYAPP_MYSQL_HOST');
        $this->db      = getenv('MYAPP_MYSQL_DB');
        $this->user    = getenv('MYAPP_MYSQL_USER');
        $this->pass    = getenv('MYAPP_MYSQL_PASS');
        $this->charset = 'utf8mb4';

        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->_pdo = new PDO($dsn, $this->user, $this->pass, $options);
    }

    public function desconectar(): void
    {
        $this->_pdo = null;
    }
}

class Libro
{
    public function __construct(
        public int $id,
        public string $titulo,
        public string $autor,
        public float $precio
    ) {}

    public string $tituloFiltrado {
        get => htmlspecialchars($this->titulo);
    }

    public string $autorFiltrado {
        get => htmlspecialchars($this->autor);
    }

    public string $precioFormateado {
        get => number_format($this->precio, 2);
    }
}

interface ILibroRepository
{
    public function getAll(): array;
    public function getById(int $id): Libro | null;
    public function add(string $titulo, string $autor, float $precio): int|null;
    public function update(int $id, string $titulo, string $autor, float $precio): bool;
    public function delete(int $id): bool;
}

class LibroRepository implements ILibroRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
        $this->database->conectar();
    }

    public function __destruct()
    {
        $this->database->desconectar();
    }

    private function existeById(int $id): bool
    {
        $sql = 'SELECT 1 FROM `libros` WHERE `id` = :id LIMIT 1';
        $stmt = $this->database->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() !== false;
    }

    public function getAll(): array
    {
        $resultado = [];
        $sql = "SELECT `id`, `titulo`, `autor`, `precio` FROM `libros` ORDER BY `titulo` ASC";
        $r = $this->database->pdo->query($sql);
        $libros = $r->fetchAll();
        foreach ($libros as $row) {
            $resultado[] = new Libro($row['id'], $row['titulo'], $row['autor'], floatval($row['precio']));
        }
        return $resultado;
    }

    public function getById(int $id): Libro | null
    {
        $sql = "SELECT `id`, `titulo`, `autor`, `precio` FROM `libros` WHERE `id`=:id";
        $stmt = $this->database->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row !== false
            ? new Libro($row['id'], $row['titulo'], $row['autor'], floatval($row['precio']))
            : null;
    }

    public function add(string $titulo, string $autor, float $precio): int|null
    {
        try {
            $sql = 'INSERT INTO `libros` (`titulo`, `autor`, `precio`) VALUES (:titulo, :autor, :precio)';
            $stmt = $this->database->pdo->prepare($sql);
            $params = [
                'titulo'  => $titulo,
                'autor' => $autor,
                'precio' => $precio
            ];
            $stmt->execute($params);
            $id = intval($this->database->pdo->lastInsertId());
            return $id;
        } catch (Throwable) {
        }
        return null;
    }

    public function update(int $id, string $titulo, string $autor, float $precio): bool
    {
        try {
            if ($this->existeById($id)) {
                $sql = 'UPDATE `libros` SET `titulo`=:titulo, `autor`=:autor, `precio`=:precio WHERE `id`=:id';
                $stmt = $this->database->pdo->prepare($sql);
                $params = [
                    'titulo'  => $titulo,
                    'autor' => $autor,
                    'precio' => $precio,
                    'id' => $id
                ];
                $stmt->execute($params);
                return true;
            }
        } catch (Throwable) {
        }
        return false;
    }

    public function delete(int $id): bool
    {
        try {
            if ($this->existeById($id)) {
                $sql = 'DELETE FROM `libros` WHERE `id`=:id';
                $stmt = $this->database->pdo->prepare($sql);
                $params = [
                    'id' => $id
                ];
                $stmt->execute($params);
                return true;
            }
        } catch (Throwable) {
        }
        return false;
    }
}

$libroRepository = new LibroRepository();

// Tengo accion ?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
} else {
    $accion = null;
}

// Crear resultado según la acción
$resultado = '';
switch ($accion) {
    case 'getAll':
        $libros = $libroRepository->getAll();
        foreach ($libros as $libro) {
            $resultado = $resultado . "<tr><td>{$libro->id}</td><td>{$libro->tituloFiltrado}</td><td>{$libro->autorFiltrado}</td><td>{$libro->precioFormateado}</td></tr>";
        }
        break;

    case 'getById':
        $id = intval($_POST['id']);
        $libro = $libroRepository->getById($id);
        $resultado = ($libro !== null)
            ? "Id: {$libro->id} | {$libro->tituloFiltrado} | {$libro->autorFiltrado} | {$libro->precioFormateado}"
            : 'ERROR: libro no encontrado';
        break;

    case 'add':
        $titulo = trim($_POST['titulo']);
        $autor = trim($_POST['autor']);
        $precio = floatval(trim($_POST['precio']));
        if (empty($titulo) || empty($autor)) {
            $resultado = 'ERROR: datos de entrada no válidos';
            break;
        }
        $id = $libroRepository->add($titulo, $autor, $precio);
        $resultado = $id !== null
            ? "libro agregado con éxito con 'id'={$id}"
            : 'ERROR: no se ha podido agregar el libro';
        break;

    case 'update':
        $id = intval($_POST['id']);
        $titulo = trim($_POST['titulo']);
        $autor = trim($_POST['autor']);
        $precio = floatval(trim($_POST['precio']));
        $resultado = $libroRepository->update($id, $titulo, $autor, $precio)
            ? 'libro actualizado con éxito'
            : 'ERROR: libro no encontrado';
        break;

    case 'delete':
        $id = intval($_POST['id']);
        $resultado = $libroRepository->delete($id)
            ? 'libro eliminado con éxito'
            : 'ERROR: libro no encontrado';
        break;
}

echo json_encode(
    ['texto' => $resultado],
    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);
