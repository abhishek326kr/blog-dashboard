function ManageProfile() {
  const [user, setUser] = React.useState(null);
  const [message, setMessage] = React.useState({ success: null, error: null });
  const [loading, setLoading] = React.useState(false);
  const [imagePreview, setImagePreview] = React.useState(null);
  const [showPasswordForm, setShowPasswordForm] = React.useState(false);

  // Fetch User Data
  React.useEffect(() => {
    fetch("../api/manage_user.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          setMessage({ error: data.error });
        } else {
          setUser(data);
          setImagePreview(data.profile_pic);
        }
      })
      .catch((error) => setMessage({ error: "Failed to load data" }));
  }, []);

  // Handle File Selection (Preview)
  const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
      setImagePreview(URL.createObjectURL(file));
    }
  };

  // Form Submit Handler
  const handleSubmit = (event) => {
    event.preventDefault();
    setLoading(true);
    const formData = new FormData(event.target);
    formData.append("action", "update_profile");

    fetch("../api/manage_user.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          setMessage({ success: data.success, error: null });
          setUser((prev) => ({ ...prev, ...data }));
          window.location.href = "dashboard.php?view=profile";
        } else {
          setMessage({ success: null, error: data.error });
        }
      })
      .catch((error) =>
        setMessage({ success: null, error: "Something went wrong!" })
      )
      .finally(() => setLoading(false));
  };

  // Handle Password Change
  const handlePasswordChange = (event) => {
    event.preventDefault();
    setLoading(true);
    const formData = new FormData(event.target);
    formData.append("action", "change_password");

    fetch("../api/manage_user.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          setMessage({ success: data.success, error: null });
          setShowPasswordForm(false);
        } else {
          setMessage({ success: null, error: data.error });
        }
      })
      .catch((error) =>
        setMessage({ success: null, error: "Something went wrong!" })
      )
      .finally(() => setLoading(false));
  };

  if (!user) return <div className="text-center text-gray-600">Loading...</div>;

  return (
    <div className="flex h-screen bg-gradient-to-r from-blue-50 to-purple-50 h-full">
      {/* Sidebar */}
        <div className="w-1/4 bg-gradient-to-b from-[#17423C] to-[#0d2926] text-white p-6 flex flex-col items-center shadow-lg h-full">
          <h2 className="text-2xl font-bold mb-4">Profile</h2>
          <img
            src={imagePreview}
            alt="Profile"
            style={{ width: "250px", height: "250px" }}
            className="rounded-full border-4 border-white object-cover shadow-lg hover:scale-105 transition-transform duration-300"
          />
          <p className="mt-4 text-xl font-semibold">{user.name}</p>
          <p className="text-md">{user.username}</p>
        </div>

        {/* Main Content */}
      <div className="w-3/4 p-6">
        <h3 className="text-2xl font-bold mb-6 text-gray-800">
          Manage Profile
        </h3>

        {message.success && (
          <div className="bg-green-100 text-green-800 p-3 rounded-lg mb-4 shadow-md">
            {message.success}
          </div>
        )}
        {message.error && (
          <div className="bg-red-100 text-red-800 p-3 rounded-lg mb-4 shadow-md">
            {message.error}
          </div>
        )}

        <form
          onSubmit={handleSubmit}
          className="space-y-6 bg-white p-8 rounded-xl shadow-lg"
        >
          <div>
            <label className="block text-gray-700 font-medium mb-2">Name</label>
            <input
              type="text"
              name="name"
              defaultValue={user.name}
              required
              className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            />
          </div>
          <div>
            <label className="block text-gray-700 font-medium mb-2">
              Username
            </label>
            <input
              type="text"
              name="username"
              defaultValue={user.username}
              required
              className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            />
          </div>
          <div>
            <label className="block text-gray-700 font-medium mb-2">
              Phone
            </label>
            <input
              type="text"
              name="phone"
              defaultValue={user.phone}
              className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            />
          </div>
          <div>
            <label className="block text-gray-700 font-medium mb-2">
              Profile Picture
            </label>
            <input
              type="file"
              name="profile_pic"
              accept="image/*"
              className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              onChange={handleFileChange}
            />
          </div>
          <button
            type="submit"
            className="w-full bg-gradient-to-b from-[#17423C] to-[#0d2926] text-white p-3 rounded-lg hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg"
          >
            {loading ? "Saving..." : "Save Changes"}
          </button>
        </form>

        {/* Toggle Password Change Form */}
        <button
          onClick={() => setShowPasswordForm(!showPasswordForm)}
          className="mt-6 w-full bg-gradient-to-r from-gray-500 to-gray-600 text-white p-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all shadow-lg"
        >
          {showPasswordForm ? "Cancel" : "Change Password"}
        </button>

        {showPasswordForm && (
          <form
            onSubmit={handlePasswordChange}
            className="space-y-6 bg-white p-8 rounded-xl shadow-lg mt-6"
          >
            <div>
              <label className="block text-gray-700 font-medium mb-2">
                Old Password
              </label>
              <input
                type="password"
                name="old_password"
                required
                className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
            </div>
            <div>
              <label className="block text-gray-700 font-medium mb-2">
                New Password
              </label>
              <input
                type="password"
                name="new_password"
                required
                className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
            </div>
            <button
              type="submit"
              className="w-full bg-gradient-to-r from-red-500 to-pink-500 text-white p-3 rounded-lg hover:from-red-600 hover:to-pink-600 transition-all shadow-lg"
            >
              {loading ? "Updating..." : "Update Password"}
            </button>
          </form>
        )}
      </div>
    </div>
  );
}
