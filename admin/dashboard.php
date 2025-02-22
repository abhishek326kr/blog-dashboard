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
    <title>Dashboard - Flexy Markets</title>
    <script src="../assets/js/react.production.min.js"></script>
    <script src="../assets/js/react-dom.production.min.js"></script>
    <script src="../assets/js/babel.min.js"></script>

    <link rel="icon" href="../assets/images/favicon.ico" type="image/png">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://cdn.tiny.cloud/1/xdzl24i0eyx673s1ukp65dwkobc1sj0foqjxgtj7fewqh0gc/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>


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
            const handleSelect = (view) => {
                onSelect(view);
                const url = new URL(window.location);
                url.searchParams.set('view', view);
                url.searchParams.delete('id'); // Remove the id parameter when switching views
                window.history.pushState({}, '', url);
            };

            React.useEffect(() => {
                const titles = {
                    'dashboard': 'Dashboard',
                    'createPosts': 'Create Post',
                    'managePosts': 'Manage Posts',
                    'profile': 'Profile',
                    'settings': 'Settings',
                    'post': 'Post'
                };

                document.title = titles[activeView] || 'Dashboard';
            }, [activeView]);


            return (
                <aside className="sidebar">
                    <div className="logo">
                        <img src="../assets/images/logo_white.png" alt="logo" />
                    </div>
                    <ul>
                        <li className={activeView === 'dashboard' ? 'active' : ''} onClick={() => handleSelect('dashboard')}>Dashboard</li>
                        <li className={activeView === 'createPosts' ? 'active' : ''} onClick={() => handleSelect('createPosts')}>Create Post</li>
                        <li className={activeView === 'managePosts' ? 'active' : ''} onClick={() => handleSelect('managePosts')}>Manage Posts</li>
                        <li className={activeView === 'profile' ? 'active' : ''} onClick={() => handleSelect('profile')}>Profile</li>
                        <li className={activeView === 'settings' ? 'active' : ''} onClick={() => handleSelect('settings')}>Settings</li>
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


        function ManageProfile() {
            const [content, setContent] = React.useState('');

            React.useEffect(() => {
                fetch('manage_user.php')
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }


        function CreatePosts() {
            const [content, setContent] = React.useState('');

            React.useEffect(() => {
                fetch('../blog/create_post.php')
                    .then(response => response.text())
                    .then(data => {
                        setContent(data);

                        // Wait a bit to ensure content is in the DOM, then initialize TinyMCE
                        setTimeout(() => {
                            if (window.tinymce) {
                                window.tinymce.remove(); // Remove any existing TinyMCE instances
                                window.tinymce.init({
                                    selector: '#content',
                                    plugins: 'advlist autolink lists link image charmap preview hr anchor pagebreak code paste',
                                    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview code',
                                    paste_data_images: false,
                                    images_upload_url: '../blog/upload.php',
                                    automatic_uploads: true,
                                    height: 400
                                });
                            }
                        }, 500);
                    });
            }, []);

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }

        function Post() {
            const [content, setContent] = React.useState('');

            React.useEffect(() => {
                const params = new URLSearchParams(window.location.search);
                const postId = params.get('id');
                fetch(`../blog/post.php?id=${postId}`)
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }


        function EditPost() {
            const [content, setContent] = React.useState('');

            React.useEffect(() => {
                const params = new URLSearchParams(window.location.search);
                const postId = params.get('id');

                fetch(`../blog/edit_post.php?id=${postId}`)
                    .then(response => response.text())
                    .then(data => {
                        setContent(data);
                    });
            }, []);

            React.useEffect(() => {
                if (content) {
                    setTimeout(() => {
                            if (window.tinymce) {
                                window.tinymce.remove(); // Remove any existing TinyMCE instances
                                window.tinymce.init({
                                    selector: '#content',
                                    plugins: 'advlist autolink lists link image charmap preview hr anchor pagebreak code paste',
                                    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview code',
                                    paste_data_images: false,
                                    images_upload_url: '../blog/upload.php',
                                    automatic_uploads: true,
                                    height: 400
                                });
                            }
                        }, 500);
                }
            }, [content]); // Ye ensure karega ki TinyMCE tab initialize ho jab content update ho

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }


        function Dashboard({ view }) {
            let content;
            switch (view) {

                case 'profile':
                    content = <ManageProfile />;
                    break;



                case 'editPost':
                    content = <EditPost />;
                    break;

                case 'post':
                    content = <Post />;
                    break;

                case 'createPosts':
                    content = <CreatePosts />;
                    break;

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
            const [view, setView] = React.useState(() => {
                const params = new URLSearchParams(window.location.search);
                return params.get('view') || 'dashboard';
            });

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