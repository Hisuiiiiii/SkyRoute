<footer class="site-footer">
  <div class="footer-content">
    <p>© 2025 <strong>SkyRoute</strong> | All Rights Reserved</p>
    <p>
      University of St. La Salle  • 
      Developed by <strong>Group 6</strong>
    </p>
  </div>
</footer>

<style>
  /* Footer Styling */
  .site-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    color: #000;
    text-align: center;
    font-family: 'Poppins', sans-serif;
    z-index: 9999;
  }

  .footer-content {
    max-width: 900px;
    margin: 0 auto;
    font-size: 14px;
    line-height: 1.4;
  }

  .footer-content a {
    color: #000;
    text-decoration: underline;
  }

  .footer-content a:hover {
    text-decoration: none;
  }

  /* Ensure content never hides behind the footer */
  body {
    margin: 0;
    padding-bottom: 70px; /* same as footer height */
    min-height: 100vh;
    box-sizing: border-box;
  }
</style>
