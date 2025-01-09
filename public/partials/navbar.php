<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-pink navbar-light" style="background-color: #ffccd5; font-family: 'Comic Sans MS', cursive;">
  <!-- Container wrapper -->
  <div class="container-fluid">

    <!-- Navbar brand -->
    <a class="navbar-brand" href="index.php?page=etusivu">
      <img src="images/kitty-logo.png" alt="Hello Kitty" style="height: 40px;"> <!-- Lisää sopiva logo -->
    </a>

    <!-- Toggle button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fas fa-bars" style="color: white;"></i>
    </button>

    <!-- Collapsible wrapper -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
      

        <!-- Shop -->
        <li class="nav-item">
          <a class="nav-link text-white" href="index.php?page=product">Shop</a>
        </li>

        <!-- Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Categories
          </a>
          <!-- Dropdown menu -->
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">Plush Toys</a></li>
            <li><a class="dropdown-item" href="#">Accessories</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Special Edition</a></li>
          </ul>
        </li>
        
      </ul>

      <!-- Icons -->
      <ul class="navbar-nav d-flex flex-row me-1">
        <li class="nav-item me-3">
          <a class="nav-link text-white" href="#"><i class="fas fa-shopping-cart"></i></a>
        </li>
      </ul>

      <!-- Search and Profile -->
      <form class="d-flex align-items-center">
        <input type="search" class="form-control" placeholder="Search Hello Kitty" aria-label="Search" style="border-radius: 20px;">
        <button class="btn btn-white ms-2" type="submit"><i class="fas fa-search"></i></button>
        <!-- Profile Icon -->
        <?php
        $profileUrl = "index.php?page=profile"; // Default profile URL for regular users
        if (isset($_SESSION['SESS_ROLE']) && $_SESSION['SESS_ROLE'] === 'admin') {
            $profileUrl = "index.php?page=admin_profile"; // Admin profile URL
        }
        ?>
        <a href="<?php echo $profileUrl; ?>" class="btn btn-white ms-2">
          <i class="fas fa-user-circle" style="font-size: 1.5rem; color: #ff6f91;"></i>
        </a>
      </form>
    </div>
  </div>
  <!-- Container wrapper -->
</nav>
<!-- Navbar -->

<style>
  .bg-pink {
    background-color: #ffccd5 !important;
  }
  .text-white {
    color: #ffffff !important;
  }
  .btn-white {
    background-color: #ffffff;
    border: 1px solid #ff6f91;
    color: #ff6f91;
  }
  .btn-white:hover {
    background-color: #ff6f91;
    color: #ffffff;
  }
</style>
