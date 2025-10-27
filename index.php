<?php
session_start();
//require 'config.php';//
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Flight Scheduler</title>
  <style>
    :root {
      --primary: #007bff;
      --primary-dark: #0056b3;
      --white: #ffffff;
      --text: #1b1f23;
      --border: #e0e6ed;
      --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      --radius: 14px;
    }

    body {
      font-family: "Inter", "Segoe UI", Roboto, sans-serif;
      margin: 0;
      min-height: 100vh;
      background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1600&q=80');
      background-size: cover;
      background-attachment: fixed;
      background-position: center;
      color: var(--text);
      position: relative;
    }

    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(6px);
      z-index: -1;
    }

    /* NAVBAR */
    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background: rgba(255, 255, 255, 0.95);
      border-bottom: 1px solid var(--border);
      backdrop-filter: blur(8px);
      z-index: 100;
    }

    .nav-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 90%;
      max-width: 1000px;
      margin: 0 auto;
      padding: 18px 0;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 18px;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--text);
      font-weight: 500;
      transition: color 0.25s;
    }

    .nav-links a:hover {
      color: var(--primary-dark);
    }

    .username {
      background: #f3f5f9;
      padding: 6px 14px;
      border-radius: 999px;
      font-weight: 600;
      color: #444;
    }

    .logout-btn {
      background: var(--primary);
      color: var(--white) !important;
      padding: 8px 18px;
      border-radius: var(--radius);
      font-weight: 600;
      transition: background 0.25s;
    }

    .logout-btn:hover {
      background: var(--primary-dark);
    }

    /* MAIN CONTAINER */
    .container {
      margin: 120px auto 60px;
      width: 90%;
      max-width: 1000px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 40px 50px;
      position: relative;
      z-index: 1;
      text-align: center;
    }

    h1 {
      font-size: 2rem;
      color: var(--primary-dark);
      margin-bottom: 30px;
    }

    /* SEARCH FORM */
    form {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: flex-end;
      gap: 16px;
      margin-bottom: 25px;
    }
    
    /* New style to group the date/status/button fields together */
    .flex-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: flex-end;
        gap: 16px;
        /* The existing form gap handles the spacing, so this is just structural */
    }


    .filter-group {
      display: flex;
      flex-direction: column;
      text-align: left;
    }

    .filter-group label {
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--text);
      margin-bottom: 4px;
    }

    .filter-group input,
    .filter-group select {
      padding: 12px 14px;
      border-radius: var(--radius);
      border: 1px solid var(--border);
      font-size: 15px;
      width: 170px;
      transition: border-color 0.2s;
    }

    .filter-group input:focus,
    .filter-group select:focus {
      border-color: var(--primary);
      outline: none;
    }

    button {
      height: 46px;
      padding: 0 28px;
      border-radius: var(--radius);
      font-weight: 600;
      background: var(--primary);
      color: var(--white);
      border: none;
      cursor: pointer;
      transition: background 0.25s, transform 0.1s;
    }

    button:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }

    @media (max-width: 900px) {
      form, .flex-row {
        flex-direction: column;
        align-items: stretch;
      }
      .filter-group input,
      .filter-group select,
      button {
        width: 100%;
      }
    }

    /* TABLE STYLING */
    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow);
      margin-top: 25px;
    }

    th {
      padding: 15px 12px;
      text-align: center;
      background: var(--primary);
      color: var(--white);
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.9rem;
    }

    td {
      padding: 15px 12px;
      text-align: center;
      border-bottom: 1px solid #f0f0f0;
      background: #fff;
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover td {
      background: #f9f9f9;
    }

    /* STATUS COLORS */
    .status-scheduled { color: #007bff; font-weight: bold; }
    .status-active { color: #ffc107; font-weight: bold; }
    .status-landed { color: #28a745; font-weight: bold; }
    .status-cancelled { color: #dc3545; font-weight: bold; }
    .status-diverted { color: #fd7e14; font-weight: bold; }
    .status-unknown { color: #6c757d; font-weight: bold; }

    /* AIRPORT CODES SECTION */
    .codes-toggle {
      display: inline-block;
      color: var(--primary-dark);
      font-weight: 600;
      cursor: pointer;
      margin-top: 15px;
      transition: color 0.25s;
    }

    .codes-toggle:hover {
      color: var(--primary);
    }

    .codes-list {
      display: none;
      margin-top: 20px;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
    }

    #results {
      margin-top: 35px;
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <div class="nav-content">
      <div class="logo">SkyRoute</div>
      <div class="nav-links">
        <?php if (isset($_SESSION['user'])): ?>
          <span class="username"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
          <a href="logout.php" class="logout-btn">Log Out</a>
        <?php else: ?>
          <a href="login.php">Log In</a>
          <a href="register.php">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="container">
    <h1>Search Flights</h1>

    <form id="searchForm">
      <div class="filter-group">
        <label for="origin">Origin</label>
        <input type="text" id="origin" name="origin" placeholder="e.g. MNL">
      </div>

      <div class="filter-group">
        <label for="destination">Destination</label>
        <input type="text" id="destination" name="destination" placeholder="e.g. HKG">
      </div>
        
      <div class="flex-row">
          <div class="filter-group">
            <label for="dep_date">🛫 Departure Date</label>
            <input type="date" id="dep_date" name="dep_date">
          </div>
    
          <div class="filter-group">
            <label for="arr_date">🛬 Arrival Date</label>
            <input type="date" id="arr_date" name="arr_date">
          </div>
          
          <div class="filter-group">
            <label for="status">Status</label>
            <select id="status" name="status">
              <option value="">Any</option>
              <option value="scheduled">Scheduled</option>
              <option value="active">Active</option>
              <option value="landed">Landed</option>
              <option value="cancelled">Cancelled</option>
              <option value="diverted">Diverted</option>
            </select>
          </div>
          <button type="submit">Search</button>
      </div>
      </form>

    <span class="codes-toggle" onclick="toggleCodes()">📘 View Common Airport Codes</span>

    <div id="codesList" class="codes-list">
      <table>
        <tr><th>Code</th><th>City / Country</th></tr>
        <tr><td>MNL</td><td>Manila, Philippines</td></tr>
        <tr><td>CEB</td><td>Cebu, Philippines</td></tr>
        <tr><td>DVO</td><td>Davao, Philippines</td></tr>
        <tr><td>HKG</td><td>Hong Kong</td></tr>
        <tr><td>NRT</td><td>Tokyo Narita, Japan</td></tr>
        <tr><td>ICN</td><td>Seoul, South Korea</td></tr>
        <tr><td>DXB</td><td>Dubai, UAE</td></tr>
        <tr><td>LAX</td><td>Los Angeles, USA</td></tr>
        <tr><td>SIN</td><td>Singapore</td></tr>
        <tr><td>BKK</td><td>Bangkok, Thailand</td></tr>
        <tr><td>KUL</td><td>Kuala Lumpur, Malaysia</td></tr>
        <tr><td>SYD</td><td>Sydney, Australia</td></tr>
      </table>
    </div>

    <div id="results"></div>
  </div>

  <script>
  function toggleCodes() {
    const list = document.getElementById('codesList');
    list.style.display = list.style.display === 'none' || list.style.display === '' ? 'block' : 'none';
  }

  document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const origin = document.getElementById('origin').value.trim();
    const destination = document.getElementById('destination').value.trim();
    const dep_date = document.getElementById('dep_date').value;
    const arr_date = document.getElementById('arr_date').value;
    const status = document.getElementById('status').value;
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '<p style="color:#007bff;">Searching flights...</p>';

    try {
      const query = new URLSearchParams({ origin, destination, dep_date, arr_date, status });
      const response = await fetch(`search.php?${query.toString()}`);
      const data = await response.json();

      if (!data.success) {
        resultsDiv.innerHTML = `<p style="color:#dc3545;">${data.message}</p>`;
        return;
      }

      if (data.flights.length === 0) {
        resultsDiv.innerHTML = '<p>No flights found for that search.</p>';
        return;
      }

      let tableHTML = `
        <table>
          <tr>
            <th>Airline</th>
            <th>Flight No</th>
            <th>Departure</th>
            <th>Departure Time</th>
            <th>Arrival</th>
            <th>Arrival Time</th>
            <th>Status</th>
          </tr>
      `;

      data.flights.forEach(f => {
        const statusClass = "status-" + (f.status ? f.status.toLowerCase() : "unknown");
        tableHTML += `
          <tr>
            <td>${f.airline}</td>
            <td>${f.flight}</td>
            <td>${f.departure}</td>
            <td>${f.departure_time}</td>
            <td>${f.arrival}</td>
            <td>${f.arrival_time}</td>
            <td class="${statusClass}">${f.status}</td>
          </tr>`;
      });

      tableHTML += '</table>';
      resultsDiv.innerHTML = tableHTML;
    } catch (err) {
      console.error('Error fetching data:', err);
      resultsDiv.innerHTML = '<p style="color:#dc3545;">An error occurred. Check console for details.</p>';
    }
  });
  </script>
 
  <?php include 'footer.php'; ?>
</body>
</html>