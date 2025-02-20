<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<?php 
include '../config/db.php';

$admin_id = $_SESSION['admin_id'];
$query = "SELECT name FROM admins WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$admin_name = $row['name'];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://unpkg.com/react@17/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/babel-standalone@6.26.0/babel.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/style.css">

    
</head>

<body>
    <div id="root"></div>

    <script type="text/babel">
        function Header() {
            return (
                <header className="header">
                    <div className="header-title">
                     
                                                Hi, <?php echo htmlspecialchars($admin_name); ?>
                    </div>
                    <div className="header-right">
                        <button className="notification-button">
                            <i className="fas fa-bell notification-icon"></i>
                        </button>

                        <i className="fas fa-user-circle user-icon"></i>
                        <span className="admin-text">Admin</span>
                    </div>
                </header>
            );
        }

        function Sidebar({ onSelect, activeView }) {
            return (
                <aside className="sidebar">
                    <div className="logo">
                        <img src="../assets/images/logo_white.png" alt="logo" />
                    </div>
                    <ul>
                        <li className={activeView === 'dashboard' ? 'active' : ''} onClick={() => onSelect('dashboard')}>Dashboard</li>
                        <li className={activeView === 'managePosts' ? 'active' : ''} onClick={() => onSelect('managePosts')}>Manage Posts</li>
                        <li className={activeView === 'profile' ? 'active' : ''} onClick={() => onSelect('profile')}>Profile</li>
                        <li className={activeView === 'settings' ? 'active' : ''} onClick={() => onSelect('settings')}>Settings</li>
                        <li onClick={() => window.location.href = '../auth/logout.php'}>Logout</li>
                    </ul>
                </aside>
            );
        }

        function DashboardContent() {
            const [content, setContent] = React.useState('');

            React.useEffect(() => {
                fetch('dashboard_content.php')
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }

        function ManagePosts() {
            const [content, setContent] = React.useState('');

            React.useEffect(() => {
                fetch('manage_posts.php')
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }

        function Dashboard({ view }) {
            let content;
            switch (view) {
                case 'managePosts':
                    content = <ManagePosts />;
                    break;
                case 'dashboard':
                default:
                    content = <DashboardContent />;
                    break;
            }
            return (
                <div className="content">
                    <Header />
                    <main className="main">
                        {content}
                    </main>
                </div>
            );
        }

        function App() {
            const [view, setView] = React.useState('dashboard');

            return (
                <div style={{ display: "flex" }}>
                    <Sidebar onSelect={setView} activeView={view} />
                    <Dashboard view={view} />
                </div>
            );
        }

        ReactDOM.render(<App />, document.getElementById("root"));

      
    </script>
</body>

</html>