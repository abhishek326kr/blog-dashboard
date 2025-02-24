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
            const [searchTerm, setSearchTerm] = React.useState("");
            const [searchResults, setSearchResults] = React.useState([]);
            const [darkMode, setDarkMode] = React.useState(false);
            const [showProfileMenu, setShowProfileMenu] = React.useState(false);
            const [showSearchResults, setShowSearchResults] = React.useState(false);

            React.useEffect(() => {
                document.body.className = darkMode ? "dark-mode" : "";
            }, [darkMode]);

            // Function to fetch search results from backend
            const fetchSearchResults = async (query) => {
                if (!query.trim()) {
                    setSearchResults([]);
                    return;
                }

                try {
                    const response = await fetch(`http://localhost/blog-dashboard/api/search.php?query=${encodeURIComponent(query)}`);
                    const data = await response.json();

                    if (data.results) {
                        setSearchResults(data.results);
                    } else {
                        setSearchResults([]); // No results
                    }
                } catch (error) {
                    console.error("Error fetching search results:", error);
                    setSearchResults([]); // Error case
                }
            };


            // Debounce search to avoid excessive API calls
            React.useEffect(() => {
                const delayDebounce = setTimeout(() => {
                    if (searchTerm) {
                        fetchSearchResults(searchTerm);
                    } else {
                        setSearchResults([]);
                    }
                }, 300); // 300ms delay to optimize API calls

                return () => clearTimeout(delayDebounce);
            }, [searchTerm]);

            return (
                <header className="header">
                    <div className="search-container">
                        <input
                            type="text"
                            className="search-bar"
                            placeholder="Search blogs..."
                            value={searchTerm}
                            onChange={(e) => {
                                setSearchTerm(e.target.value);
                                setShowSearchResults(e.target.value.length > 0);
                            }}
                        />
                        <div className={`search-results ${showSearchResults ? "active" : ""}`}>
                            {searchTerm && <p>Showing results for "{searchTerm}"</p>}
                            {searchResults.length > 0 ? (
                                <ul>
                                    {searchResults.map((result) => (
                                        <li key={result.id}>
                                            <a href={`/blog/${result.id}`}>{result.title}</a>
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                searchTerm && <p>No results found.</p>
                            )}
                        </div>
                    </div>

                    <div className="header-right">
                        <i className="fas fa-bell notif-bell"></i>

                        <button className="btn" onClick={() => setDarkMode(!darkMode)}>
                            {darkMode ? "🌞 Light Mode" : "🌙 Dark Mode"}
                        </button>

                        <button className="btn" onClick={() => setShowProfileMenu(!showProfileMenu)}>
                            <i className="fas fa-user-circle user-icon"></i> Hi <span id="admin-name"></span>
                        </button>

                        <div className={`profile-menu ${showProfileMenu ? "active" : ""}`}>
                            <p><a href="dashboard.php?view=profile">Profile</a></p>
                            <p><a href="dashboard.php?view=settings">Settings</a></p>
                            <p><a href="logout.php">Logout</a></p>
                        </div>
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
                        <li className={activeView === 'leaderboard' ? 'active' : ''} onClick={() => handleSelect('leaderboard')}>Leaderboard</li>
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


        function Leaderboard() {
            const [data, setData] = React.useState({ trendingBlogs: [], topUsers: [] });

            React.useEffect(() => {
                fetch("get_leaderboard.php")
                    .then(response => response.json())
                    .then(data => setData(data));
            }, []);

            return (
                <div className="leaderboard">
                    <h2>🏆 Leaderboard</h2>

                    <div className="leaderboard-section">
                        <h3>🔥 Trending Blogs</h3>
                        <ul>
                            {data.trendingBlogs.map((blog, index) => (
                                <li key={index}>{index + 1}. {blog.title} - {blog.views} Views</li>
                            ))}
                        </ul>
                    </div>

                    <div className="leaderboard-section">
                        <h3>📝 Top Contributors</h3>
                        <ul>
                            {data.topUsers.map((user, index) => (
                                <li key={index}>{index + 1}. {user.author} - {user.posts} Posts</li>
                            ))}
                        </ul>
                    </div>
                </div>
            );
        }


        function Dashboard({ view }) {
            let content;
            switch (view) {

                case 'leaderboard':
                    content = <Leaderboard />;
                    break;

                case 'settings':
                    content = <div>Settings</div>;
                    break;

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

        // Set the admin name after rendering
        document.getElementById("admin-name").textContent = "<?php echo $admin_name; ?>";




    </script>
</body>

</html>