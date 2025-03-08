<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$admin_id = $_SESSION['admin_id'];
$query = "SELECT name, profile_pic FROM admins WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$admin_name = $row['name'];
$profile_pic = $row['profile_pic'];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Flexy Markets</title>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
    <script src="../assets/js/babel.min.js"></script>

    <link rel="icon" href="../assets/images/favicon.ico" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../config/tinymce/js/tinymce/tinymce.min.js"
    referrerpolicy="origin"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/scripts.js"></script>

    <script src="https://unpkg.com/feather-icons"></script>

    <!-- Tailwind CSS (Optional, for styling) -->

</head>

<body>
    <div id="root"></div>

    <script type="text/babel">
        function Header({ adminName }) {
            const [searchTerm, setSearchTerm] = React.useState("");
            const [searchResults, setSearchResults] = React.useState([]);
            const [showProfileMenu, setShowProfileMenu] = React.useState(false);
            const [showSearchResults, setShowSearchResults] = React.useState(false);

            // Load user preference from localStorage
            const [darkMode, setDarkMode] = React.useState(() => {
                const savedMode = localStorage.getItem("darkMode");
                return savedMode === "true"; // User preference
            });

            // Apply dark mode to body
            React.useEffect(() => {
                document.body.classList.toggle("dark-mode", darkMode);
                localStorage.setItem("darkMode", darkMode);
            }, [darkMode]);

            // Function to fetch search results
            const fetchSearchResults = (query) => {
                if (query.length < 3) {
                    setSearchResults([]);
                    return;
                }

                fetch(`../api/search.php?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        setSearchResults(data.results || []);
                    })
                    .catch(error => console.error("Error fetching search results:", error));
            };

            return (
                <header className="header">
                    <div className="search-container">
                        <input
                            type="text"
                            className="search-bar"
                            placeholder="Search blogs..."
                            value={searchTerm}
                            onChange={(e) => {
                                const value = e.target.value;
                                setSearchTerm(value);
                                setShowSearchResults(value.length > 0);
                                fetchSearchResults(value);
                            }}
                        />
                        <div className={`search-results ${showSearchResults ? "active" : ""}`}>
                            {searchTerm && <p>Showing results for "{searchTerm}"</p>}
                            {searchResults.length > 0 ? (
                                <ul>
                                    {searchResults.map((result) => (
                                        <li key={result.id}>
                                            <a className="post-title-link" href={`/blog/${result.id}`}>
                                                {result.title}
                                            </a>
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                searchTerm && <p>No results found.</p>
                            )}
                        </div>
                    </div>

                    <div className="header-right">

                        <button className="btn" onClick={() => setDarkMode(!darkMode)}>
                            {darkMode ? "🌞 Light Mode" : "🌙 Dark Mode"}
                        </button>

                        <button className="btn" onClick={() => setShowProfileMenu(!showProfileMenu)}>
                            <img src="../uploads/<?php echo $profile_pic; ?>" alt="Profile Picture" className="profile-pic-icon" />
                            Hi, <?php echo $admin_name; ?>
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
                if (activeView === 'createPosts' && !confirm('Are you sure you want to leave this page? Unsaved changes will be lost.')) {
                    return;
                }
                if (activeView === 'editPost' && !confirm('Are you sure you want to leave this page? Unsaved changes will be lost.')) {
                    return;
                }
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
                        <li className={activeView === 'instantIndex' ? 'active' : ''} onClick={() => handleSelect('instantIndex')}>Instant Indexing</li>
                        <li className={activeView === 'manageComments' ? 'active' : ''} onClick={() => handleSelect('manageComments')}>Manage Comments</li>
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
            const contentRef = React.useRef(null);

            React.useEffect(() => {
                fetch('dashboard_content.php')
                    .then(response => response.text())
                    .then(data => {
                        setContent(data);

                        // Wait for React to render the content, then execute scripts
                        setTimeout(() => executeScripts(), 100);
                    });
            }, []);

            function executeScripts() {
                const scripts = contentRef.current.querySelectorAll("script");

                scripts.forEach(oldScript => {
                    const newScript = document.createElement("script");
                    newScript.text = oldScript.text;  // Copy inline script content
                    document.body.appendChild(newScript);  // Execute script
                    oldScript.remove();  // Remove old script to avoid duplication
                });
            }

            return <div ref={contentRef} dangerouslySetInnerHTML={{ __html: content }} />;
        }


        function ManageProfile() {
            const [profile, setProfile] = React.useState(null);

            React.useEffect(() => {
                fetch('../api/manage_user.php')
                    .then(response => response.json())
                    .then(data => setProfile(data))
                    .catch(error => console.error('Error fetching profile:', error));
            }, []);

            if (!profile) {
                return <div className="profile-loading">Loading...</div>;
            }

            return (
                <div className="profile-container">
                    <div className="profile-card">
                        <h2 className="profile-title">Profile Details</h2>
                        <div className="profile-content">
                            <div className="profile-picture-container">
                                <img src={`../uploads/${profile.profile_pic}`} alt="Profile Picture" className="profile-picture" />
                            </div>
                            <div className="profile-info">
                                <p><strong>Name:</strong> {profile.name}</p>
                                <p><strong>Username:</strong> {profile.username}</p>
                                <p><strong>Email:</strong> {profile.email}</p>
                                <p><strong>Phone:</strong> {profile.phone}</p>
                            </div>

                        </div>
                        <button className="profile-edit-button" onClick={() => window.location.href = 'manage_user.html'}>Edit Profile</button>
                    </div>
                </div>
            );
        }

        function Settings() {
            const [content, setContent] = React.useState('');
            const contentRef = React.useRef(null);

            React.useEffect(() => {
                fetch('settings.php')
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            return <div ref={contentRef} dangerouslySetInnerHTML={{ __html: content }} />;
        }



        function ManageComments() {
            const [content, setContent] = React.useState('');
            const contentRef = React.useRef(null);

            React.useEffect(() => {
                fetch('../blog/manage_comments.php')
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            return <div ref={contentRef} dangerouslySetInnerHTML={{ __html: content }} />;
        }



        function CreatePosts() {
            const [content, setContent] = React.useState('');
            const contentRef = React.useRef(null);

            React.useEffect(() => {

                fetch('../blog/create_post.php')
                    .then(response => response.text())
                    .then(data => {
                        setContent(data);
                    });
            }, []);

            React.useEffect(() => {
                if (contentRef.current) {
                    setTimeout(() => {
                        if (window.tinymce) {
                            window.tinymce.remove();
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
            }, [content]);

            return (
                <div ref={contentRef} dangerouslySetInnerHTML={{ __html: content }} />
            );
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
            const contentRef = React.useRef(null);

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
                if (contentRef.current) {
                    setTimeout(() => {
                        if (window.tinymce) {
                            window.tinymce.remove();
                            window.tinymce.init({
                                selector: '#content',
                                menubar: false,
                                plugins: 'lists link image preview',
                                toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview',
                                height: 300
                            });
                        }
                    }, 500);
                }
            }, [content]);

            return <div ref={contentRef} dangerouslySetInnerHTML={{ __html: content }} />;
        }

        function InstantIndex() {
            const [content, setContent] = React.useState('');

            React.useEffect(() => {
                const params = new URLSearchParams(window.location.search);
                const postId = params.get('id');
                fetch(`../api/instant_indexing.php`)
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            function executeScripts() {
                const scripts = contentRef.current.querySelectorAll("script");

                scripts.forEach(oldScript => {
                    const newScript = document.createElement("script");
                    newScript.text = oldScript.text;  // Copy inline script content
                    document.body.appendChild(newScript);  // Execute script
                    oldScript.remove();  // Remove old script to avoid duplication
                });
            }

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }


        function Leaderboard() {
            const [content, setContent] = React.useState('');

            React.useEffect(() => {
                const params = new URLSearchParams(window.location.search);
                const postId = params.get('id');
                fetch('leaderboard.php')
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }

        function ManagePosts() {
            const [content, setContent] = React.useState('');
            const contentRef = React.useRef(null);

            React.useEffect(() => {
                fetch('manage_posts.php')
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            return <div ref={contentRef} dangerouslySetInnerHTML={{ __html: content }} />;
        }



        function Dashboard({ view, adminName }) {
            let content;
            switch (view) {
                case 'manageComments':
                    content = <ManageComments />;
                    break;
                case 'instantIndex':
                    content = <InstantIndex />;
                    break;
                case 'leaderboard':
                    content = <Leaderboard />;
                    break;
                case 'settings':
                    content = <Settings />;
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

        function App({ adminName }) {
            const [view, setView] = React.useState(() => {
                const params = new URLSearchParams(window.location.search);
                return params.get('view') || 'dashboard';
            });

            return (
                <div style={{ display: "flex" }}>
                    <Sidebar onSelect={setView} activeView={view} />
                    <Dashboard view={view} adminName={adminName} />
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById("root"));
        root.render(<App adminName="<?php echo $admin_name; ?>" />);



    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Fetch data from PHP API
            fetch("../api/fetch_seo_data.php")
                .then(response => response.json())  // Convert response to JSON
                .then(data => {
                    if (data.error) {
                        console.error("Error fetching data:", data.error);
                        document.getElementById("search_traffic").innerText = "Error";
                        document.getElementById("search_impressions").innerText = "Error";
                        document.getElementById("total_keywords").innerText = "Error";
                        document.getElementById("avg_position").innerText = "Error";
                    } else {
                        // Update dashboard values
                        document.getElementById("search_traffic").innerText = data.search_traffic;
                        document.getElementById("search_impressions").innerText = data.search_impressions;
                        document.getElementById("total_keywords").innerText = data.total_keywords;
                        document.getElementById("avg_position").innerText = data.avg_position;
                    }
                })
                .catch(error => console.error("Fetch Error:", error));
        });
    </script>

</body>

</html>