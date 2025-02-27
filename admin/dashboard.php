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
    <script src="https://unpkg.com/react@17/umd/react.production.min.js" crossorigin></script>
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
                            <img src="../uploads/<?php echo $profile_pic; ?>" alt="Profile Picture" className="profile-pic-icon" />
                            Hi <span id="admin-name"></span>
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
                    .then(data => setContent(data));
            }, []);

            return <div ref={contentRef} dangerouslySetInnerHTML={{ __html: content }} />;
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



        function ManageProfile() {
    const [content, setContent] = React.useState('');

    React.useEffect(() => {
        let isMounted = true;
        fetch('manage_user.php')
            .then(response => response.text())
            .then(data => {
                if (isMounted) setContent(data);
            });
        return () => { isMounted = false };
    }, []);

    React.useEffect(() => {
        // Toggle form function
        function toggleEditForm() {
            const profileSection = document.getElementById('profileSection');
            const editForm = document.getElementById('editForm');
            [profileSection, editForm].forEach(el => {
                if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
            });
        }

        // Section navigation
        function showSection(sectionId) {
            document.querySelectorAll('.profile-content > div').forEach(div => {
                if (div) div.style.display = 'none';
            });
            const section = document.getElementById(sectionId + 'Section');
            if (section) section.style.display = 'block';
        }

        // Image preview handler
        const handleImagePreview = (e) => {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.querySelector('.profile-pic');
                    if (img) img.src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        };

        // Get DOM elements
        const editBtn = document.getElementById('editProfileBtn');
        const saveBtn = document.getElementById('saveChangesBtn');
        const profilePicInput = document.querySelector('input[name="profile_pic"]');
        const navLinks = document.querySelectorAll('[data-section]');

        // Add event listeners
        if (editBtn) editBtn.addEventListener('click', toggleEditForm);
        if (profilePicInput) profilePicInput.addEventListener('change', handleImagePreview);
        if (navLinks) {
            navLinks.forEach(link => {
                link.addEventListener('click', () => showSection(link.dataset.section));
            });
        }

        // Form submission handler
        const handleSubmit = (e) => {
            e.preventDefault();
            const formData = new FormData(document.getElementById('editForm'));
            
            fetch('manage_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.text();
            })
            .then(() => {
                alert('Profile updated successfully!');
                location.reload();
            })
            .catch(error => {
                alert(`Error: ${error.message}`);
            });
        };

        if (saveBtn) saveBtn.addEventListener('click', handleSubmit);

        // Cleanup function
        return () => {
            if (editBtn) editBtn.removeEventListener('click', toggleEditForm);
            if (profilePicInput) profilePicInput.removeEventListener('change', handleImagePreview);
            if (navLinks) {
                navLinks.forEach(link => {
                    link.removeEventListener('click', () => showSection(link.dataset.section));
                });
            }
            if (saveBtn) saveBtn.removeEventListener('click', handleSubmit);
        };
    }); // Re-run when content updates

    return <div dangerouslySetInnerHTML={{ __html: content }} />;
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

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }


        function Leaderboard() {
            const [content, setContent] = React.useState('');

            React.useEffect(() => {
                fetch('../api/leaderboard.php')
                    .then(response => response.text())
                    .then(data => setContent(data));
            }, []);

            return <div dangerouslySetInnerHTML={{ __html: content }} />;
        }



        function Dashboard({ view }) {
            let content;
            switch (view) {
                case 'instantIndex':
                    content = <InstantIndex />;
                    break;
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