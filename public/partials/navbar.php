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
      <a class="navbar-brand me-3" href="index.php?page=etusivu">
        <img src="images/kitty-logo.png" alt="Hello Kitty Logo" style="height: 35px;">
      </a>

      <!-- Left Links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-white" href="index.php?page=etusivu">Hello Kitty Verkkokauppa</a>
        </li>
      </ul>
    </div>

    <!-- Search Bar and Categories in the Center -->
    <div class="d-flex align-items-center" style="flex-grow: 1; justify-content: center;">
      <!-- Categories Dropdown -->
      <ul class="navbar-nav ms-2">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Categories
        </a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php
            $link = getDbConnection(); // Connect to the database
            $categoryQuery = "SELECT * FROM categories WHERE parent_id IS NULL"; // Get only main categories
            $categoryResult = mysqli_query($link, $categoryQuery);

            while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                $categoryId = $categoryRow['category_id'];
                $categoryName = htmlspecialchars($categoryRow['name']);

                // Check if this category has subcategories
                $subQuery = "SELECT * FROM categories WHERE parent_id = $categoryId";
                $subResult = mysqli_query($link, $subQuery);
                
                if (mysqli_num_rows($subResult) > 0) {
                    echo "<li class='dropdown-submenu'>
                            <a class='dropdown-item dropdown-toggle' href='#'>$categoryName</a>
                            <ul class='dropdown-menu'>";

                    while ($subRow = mysqli_fetch_assoc($subResult)) {
                        $subId = $subRow['category_id'];
                        $subName = htmlspecialchars($subRow['name']);
                        echo "<li><a class='dropdown-item' href='index.php?page=etusivu&category=$subId'>$subName</a></li>";
                    }

                    echo "</ul></li>";
                } else {
                    echo "<li><a class='dropdown-item' href='index.php?page=etusivu&category=$categoryId'>$categoryName</a></li>";
                }
            }

            mysqli_close($link);
            ?>
        </ul>
    </li>
</ul>

      <!-- Search Bar -->
      <form action="index.php" method="GET" class="d-flex mx-2" role="search" style="width: 60%; max-width: 500px;">
        <input type="hidden" name="page" value="etusivu"> <!-- Ensures search stays on the front page -->
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
</nav>

<style>
/* General Navbar Style */
.navbar {
  background-color: #ffccd5;
  padding: 10px 20px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  font-family: 'Comic Sans MS', cursive;
}

/* Logo */
.navbar-brand img {
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
}

/* Submenus */
.navbar .dropdown-submenu .dropdown-menu {
  display: none;
  position: absolute;
  left: 100%;
  top: 0;
  visibility: hidden;
  opacity: 0;
  transition: opacity 0.3s ease, visibility 0s linear 0.3s;
}

/* Show submenu on hover */
.navbar .dropdown-submenu:hover > .dropdown-menu {
  display: block;
  visibility: visible;
  opacity: 1;
  transition-delay: 0s;
}

/* Links inside the dropdown */
.dropdown-item {
  font-size: 1rem;
  padding: 10px 20px;
  transition: background-color 0.3s ease;
}

/* Hover effect for dropdown items */
.dropdown-item:hover {
  background-color: #ff6f91;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .container-fluid {
    flex-wrap: wrap;
    justify-content: center;
  }

  .navbar .form-control {
    width: 80%;
    margin: 10px auto;
  }

  .navbar-nav {
    flex-direction: column;
    align-items: center;
  }

  .navbar .nav-link {
    margin-bottom: 10px;
  }
}
</style>
