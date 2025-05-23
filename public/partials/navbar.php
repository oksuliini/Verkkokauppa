<?php
$profileLink = "index.php?page=login"; // Default login page

if (isset($_SESSION['SESS_USER_ID'])) { // If user is logged in
    if (isset($_SESSION['SESS_ROLE'])) {
        if ($_SESSION['SESS_ROLE'] === 'admin') {
            $profileLink = "index.php?page=admin_profile"; // Admin profile page
        } elseif ($_SESSION['SESS_ROLE'] === 'customer') {
            $profileLink = "index.php?page=profile"; // Customer profile page
        }
    }
}
?>
<nav class="navbar navbar-expand-lg" style="background-color: #ffccd5; font-family: 'Comic Sans MS', cursive;">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <!-- Left Side -->
    <div class="d-flex align-items-center">
      <!-- Logo -->
      <a class="navbar-brand me-3" href="index.php?page=home">
        <img src="images/kitty-logo.png" alt="Hello Kitty Logo" style="height: 35px;">
      </a>

      <!-- Left Links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-white" href="index.php?page=home">Hello Kitty Web Store</a>
        </li>
      </ul>
    </div>

    <!-- Mobile Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Search Bar and Categories in the Center -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <div class="d-flex align-items-center" style="flex-grow: 1; justify-content: center;">
        <!-- Categories Dropdown -->
        <ul class="navbar-nav ms-2">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Categories
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">




              <?php
                $link = getDbConnection();

                $categoryQuery = "SELECT * FROM categories WHERE parent_id IS NULL"; 
                $categoryResult = mysqli_query($link, $categoryQuery);

                while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                    $categoryId = $categoryRow['category_id'];
                    $categoryName = htmlspecialchars($categoryRow['name']);

                    // Fetch subcategories
                    $subQuery = "SELECT * FROM categories WHERE parent_id = $categoryId";
                    $subResult = mysqli_query($link, $subQuery);

                    echo "<li class='dropdown-submenu'>";  
                    echo "<a class='dropdown-item' href='index.php?page=home&category=$categoryId'>$categoryName</a>";
                    
                    // Display subcategories
                    if (mysqli_num_rows($subResult) > 0) {
                        echo "<ul class='dropdown-menu subcategories'>";
                        while ($subRow = mysqli_fetch_assoc($subResult)) {
                            $subId = $subRow['category_id'];
                            $subName = htmlspecialchars($subRow['name']);
                            echo "<li><a class='dropdown-item' href='index.php?page=home&category=$subId'>$subName</a></li>";
                        }
                        echo "</ul>";
                    }

                    echo "</li>";
                }
                mysqli_close($link); // Close the connection
              ?>
            </ul>
          </li>
        </ul>

        <!-- Search Bar -->
        <form action="index.php" method="GET" class="d-flex mx-2" role="search" style="width: 60%; max-width: 500px;">
          <input type="hidden" name="page" value="home">
          <input type="search" name="search" class="form-control" placeholder="Search Hello Kitty" aria-label="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
          <button class="btn btn-white ms-2" type="submit" aria-label="Search">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>

      <!-- Cart and Profile Icons on the Right -->
      <div class="d-flex align-items-center">
        <ul class="navbar-nav">
          <!-- Cart Icon -->
          <li class="nav-item">
            <a class="nav-link text-white" href="index.php?page=cart" aria-label="Shopping Cart">
              <i class="fas fa-shopping-cart"></i>
            </a>
          </li>

          <!-- Profile Icon -->
          <li class="nav-item">
            <a href="<?php echo htmlspecialchars($profileLink); ?>" class="btn btn-white" aria-label="User Profile">
              <i class="fas fa-user-circle" style="font-size: 1.5rem; color: #ff6f91;"></i>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<style>
/* General Styles */
.navbar {
  background-color: #ffccd5;
  padding: 10px 20px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  font-family: 'Comic Sans MS', cursive;
}

.navbar .navbar-brand img {
  height: 40px;
}

/* Search Bar */
.navbar .form-control {
  border-radius: 20px;
  height: 2.5rem;
  font-size: 1rem;
}

.navbar .btn-white {
  background-color: #ffffff;
  border: 1px solid #ff6f91;
  color: #ff6f91;
  border-radius: 20px;
}

.navbar .btn-white:hover {
  background-color: #ff6f91;
  color: #ffffff;
}

/* Links */
.navbar .nav-link {
  font-weight: bold;
  font-size: 1rem;
  color: white;
  transition: background-color 0.3s ease, transform 0.2s;
  padding: 5px 10px;
  border-radius: 5px;
}

.navbar .nav-link:hover {
  background-color: #ff6f91;
  transform: scale(1.1);
}

/* Dropdown Menu */
.navbar .dropdown-menu {
  background-color: #ffe4e1;
  border-radius: 5px;
  border: none;
  display: none;
  position: absolute;
  left: 0;
  top: 100%;
  min-width: 180px;
  padding: 5px 0;
}

/* Show dropdown menu when hovering on the main category */
.navbar .dropdown:hover > .dropdown-menu {
  display: block;
}

/* Submenu Layout */
.navbar .dropdown-submenu {
  position: relative;
}

/* Submenu hidden by default */
.navbar .dropdown-submenu .dropdown-menu {
  display: none;
  position: absolute;
  left: 100%; 
  top: 0;
  min-width: 180px;
  visibility: hidden;
  opacity: 0;
  transition: opacity 0.2s ease, visibility 0s linear 0.2s;
}

/* Display subcategories when hovering */
.navbar .dropdown:hover > .dropdown-menu,
.navbar .dropdown-submenu:hover > .dropdown-menu {
  display: block !important;
  visibility: visible;
  opacity: 1;
  transition-delay: 0s;
}

.subcategories {
  background-color: #ffe4e1;
  border-radius: 5px;
  padding: 5px 10px;
}

.subcategories li {
  list-style: none;
  padding: 5px 0;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .navbar .navbar-nav {
    flex-direction: column;
    align-items: center;
    width: 100%;
  }

  .navbar .form-control {
    width: 80%;
    margin: 10px auto;
  }

  .navbar .nav-link {
    margin-bottom: 10px;
    text-align: center;
  }

  .navbar-collapse {
    justify-content: center;
    width: 100%;
  }

  .navbar-toggler {
    display: block; /* Show the hamburger icon */
  }

  /* Adjust dropdown behavior on mobile */
  .navbar .dropdown-menu {
    position: static; /* Ensure dropdown menu aligns correctly on small screens */
    background-color: #ffe4e1;
    border-radius: 5px;
  }
}
</style>