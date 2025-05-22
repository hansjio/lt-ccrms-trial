<?php
    require_once 'configs/auth.php';
    checkAuth();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap');

            * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Montserrat', sans-serif;
}
body {
    display: flex;
    background-color: rgb(249, 244, 239);
}

.current-time {
            font-size: 16px;
            margin-right: 20px;
            color:rgb(16, 69, 205);
            background-color: white;
            padding: 12px 24px;
            border-radius: 12px;
            border: 2px solid rgb(15, 1, 97);
            font-weight: bold;
            width: 150px;                     /* Fixed width */
    text-align: center;               /* Centers the time text */
    white-space: nowrap; 
        }
            .cards {
                display: flex;
        gap: 15px;
        margin-bottom: 20px;
        justify-content: center;  /* Center the cards */
        flex-wrap: wrap;  
            }

            

            .card-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                padding: 20px;
            }

            .card-text {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

        .card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(145deg,rgb(133, 185, 220),rgb(4, 38, 117));
        padding: 20px;
        flex: 1 1 250px;  /* Flex-grow, flex-shrink, and base width */
        max-width: 300px;  /* Max width for each card */
        border-radius: 12px;
        box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2),
                    -4px -4px 10px rgba(0, 0, 0, 0.15);
        font-weight: bold;
        color: rgb(252, 252, 252);
        transition: all 0.3s ease-in-out;
        height: 200px;
        font-size: 20px; 
        text-shadow: 0 0 15px #004B73,   /* Darker blue for outer shadow */
                 0 0 3px #006F9B,    /* Medium blue for closer shadow */
                 0 0 15px #003B5C,   /* Another darker blue for depth */
                 0 0 25px #002B42; 
    }
    d-text span {
                font-size: 15px;
                display: block;
            }

            .card-text p {
                margin: 0;
                font-size: 25px;
            }

            .card h1 {
                margin: 5px 0 0;
                color:white;
            }

            .card-icon img {
                width: 100px; 
                height: 100px;
                opacity: 0.60;
            }

            .charts {
                display: flex;
                gap: 30px;
                align-items: center;
                justify-content: center; 
                margin-top: 50px;
            }

            .chart-container1, .chart-container2 {
                background: white;
                padding: 50px;
                flex: 1;
                display: flex; 
                flex-direction: column; 
                justify-content: center; 
                align-items: center;
                border-radius: 12px;
                box-shadow: 4px 4px 8px rgb(25, 7, 162), -4px -4px 8px #fff;
                text-align: center;
                height: 40vh;
            }


            .chart-container1 h2,
            .chart-container2 h2 {
                text-align: center;
                color: rgb(0, 67, 200);
                font-size: 22px;
                font-weight: bold;
                letter-spacing: 1px;
                text-transform: uppercase;
                padding: 10px 15px;
                background: rgba(255, 255, 255, 0.15); 
                border-radius: 8px;
                display: inline-block;
                box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2),
                            -4px -4px 10px rgba(255, 255, 255, 0.1);
                text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4);
                transition: all 0.3s ease-in-out;
            }

            .filter-container {
    display: none; /* Initially hide the filter */
    flex-wrap: wrap;
    padding: 1.5rem 2rem;
    gap: 1.5rem;
    background: rgba(255, 255, 255, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 8px;
    backdrop-filter: blur(50px);
    box-shadow: 0 8px 20px rgba(8, 0, 255, 0.2);
    margin-bottom: 2rem;
    align-items: center;
    justify-content: center;
    width: 30%;         
    height: 50%;
    position:absolute;
    z-index: 3000;
    transition: all 0.3s ease-in-out; /* For smooth toggle */
}


.filter-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.3rem;
    position: relative;
}

.filter-group label {
    font-size: 0.9rem;
    font-weight: 600;
    color:rgb(250, 250, 250);
    position: relative;
    padding-bottom: 2px;
}

.filter-group label::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    height: 2px;
    width: 0;
    background-color:rgb(255, 255, 255);
    transition: width 0.3s ease-in-out;
}

.filter-group:hover label::after {
    width: 100%;
}

.filter-group select {
    padding: 0.6rem 1.2rem;
    font-size: 1rem;
    font-weight: 500;
    border: 2px solid transparent;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.65);
    box-shadow: inset 0 0 5px rgba(255, 140, 0, 0.2), 0 4px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    cursor: pointer;
    color:rgb(26, 0, 74);
    min-width: 130px;
}

.filter-group select:hover {
    background: rgba(255, 255, 255, 0.9);
    border-color:rgb(77, 92, 255);
    transform: scale(1.02);
}

.filter-group select:focus {
    outline: none;
    border-color:rgb(41, 5, 219);
    box-shadow: 0 0 0 4px rgba(154, 66, 255, 0.3);
    background: #fffaf5;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.filter-header {
    font-size: 1.1rem;
    font-weight: 700;
    color:rgb(13, 2, 105);
    margin-bottom: 10px;
    position: relative;
    width: 100%;
    text-align: center;
    animation: fadeIn 0.5s ease forwards;
}

#filterIcon {
            background-color:rgb(255, 255, 255); /* Tailwind 'blue-500' */
            color: rgb(15, 1, 97);
            padding: 8px; /* Tailwind 'py-3 px-6' */
            border-radius: 5px; /* Tailwind 'rounded-md' */
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem; /* Tailwind 'gap-2' */
            box-shadow: 0 0 5px rgba(66, 153, 225, 0.5); /* Subtle shadow */
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 600;
            font-size: 14px;
        }

        .fas.fa-times {
    color: #ff4d4d;         /* Red color to indicate "close" or "remove" */
    font-size: 14px;
font-weight: 600;  
width:14px;}
        </style>
    </head>
    <body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard-header">
            <span>Dashboard</span>
            <div class="header-right">
            <div id="current-time" class="current-time"></div>
            <button onclick="redirectToAuthorization(event)" class="lupon-btn">
                <?php echo htmlspecialchars($_SESSION['username']); ?> <i class="fas fa-sign-out-alt"></i>
            </button>

            </div>
        </div>

            <div class="filter-toggle">
    <button id="filterIcon" onclick="toggleFilter()">
        <i class="fas fa-filter"></i>
</div>

<div class="filter-container">
    <h3 class="filter-header">Filter by Date</h3>
    <div class="filter-group">
        <label for="startYearFilter">Start Year</label>
        <select id="startYearFilter"></select>
    </div>
    <div class="filter-group">
        <label for="startMonthFilter">Start Month</label>
        <select id="startMonthFilter">
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </div>
    <div class="filter-group">
        <label for="endYearFilter">End Year</label>
        <select id="endYearFilter"></select>
    </div>
    <div class="filter-group">
        <label for="endMonthFilter">End Month</label>
        <select id="endMonthFilter">
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </div>
</div>


            <div class="cards">
                <div class="card card1">
                    <div class="card-content">
                        <div class="card-text">
                            <span>Total</span>
                            <p>Cases Filed</p>
                            <h1 id="totalCases">0</h1>
                        </div>
                        <div class="card-icon">
                            <img src="LOGOS/cases.png" alt="Cases Icon">
                        </div>
                    </div>
                </div>
            
                <div class="card card2">
                    <div class="card-content">
                        <div class="card-text">
                            <span>Total</span>
                            <p>Criminal Cases</p>
                            <h1 id="criminalCases">0</h1>
                        </div>
                        <div class="card-icon">
                            <img src="LOGOS/criminal.png" alt="Criminal Cases Icon">
                        </div>
                    </div>
                </div>
            
                <div class="card card3">
                    <div class="card-content">
                        <div class="card-text">
                            <span>Total</span>
                            <p>Civil Cases</p>
                            <h1 id="civilCases">0</h1>
                        </div>
                        <div class="card-icon">
                            <img src="LOGOS/civil.png" alt="Civil Cases Icon">
                        </div>
                    </div>
                </div>
            
        
            </div>

            <div class="charts">
                <div class="chart-container1">
                <h2>Case Comparison</h2><br>
                <canvas id="pieChart"></canvas> 
                </div>
                <div class="chart-container2">
                    <h2> Case Filing Statistics</h2><br>
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>

function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('current-time').textContent = timeString;
  }

  // Update time every second
  setInterval(updateTime, 1000);
  updateTime(); // Run once on page load

          const startYearFilter = document.getElementById("startYearFilter");
const startMonthFilter = document.getElementById("startMonthFilter");
const endYearFilter = document.getElementById("endYearFilter");
const endMonthFilter = document.getElementById("endMonthFilter");

const currentDate = new Date();
const currentYear = currentDate.getFullYear();
const currentMonth = currentDate.getMonth() + 1; // JS months are 0-based

// Calculate start date (12 months ago)
const startDate = new Date(currentDate);
startDate.setMonth(startDate.getMonth() - 12); // includes current month
const startYear = startDate.getFullYear();
const startMonth = startDate.getMonth() + 1; // also 1-based

// Populate year selectors
for (let y = currentYear; y >= currentYear - 10; y--) {
    const optionStart = document.createElement("option");
    optionStart.value = y;
    optionStart.textContent = y;
    if (y === startYear) optionStart.selected = true;
    startYearFilter.appendChild(optionStart);

    const optionEnd = document.createElement("option");
    optionEnd.value = y;
    optionEnd.textContent = y;
    if (y === currentYear) optionEnd.selected = true;
    endYearFilter.appendChild(optionEnd);
}

// Populate month selectors with names and values
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

for (let i = 0; i < 12; i++) {
    const optionStart = document.createElement("option");
    optionStart.value = i + 1;
    optionStart.textContent = monthNames[i];
    if (i + 1 === startMonth) optionStart.selected = true;
    startMonthFilter.appendChild(optionStart);

    const optionEnd = document.createElement("option");
    optionEnd.value = i + 1;
    optionEnd.textContent = monthNames[i];
    if (i + 1 === currentMonth) optionEnd.selected = true;
    endMonthFilter.appendChild(optionEnd);
}

// Event listener when page loads
document.addEventListener("DOMContentLoaded", function () {
    fetchDashboardData();
});

// Fetch data based on selected filters
function fetchDashboardData() {
    const startYear = startYearFilter.value;
    const startMonth = startMonthFilter.value;
    const endYear = endYearFilter.value;
    const endMonth = endMonthFilter.value;

    fetch(`configs/fetch_cases.php?startYear=${startYear}&startMonth=${startMonth}&endYear=${endYear}&endMonth=${endMonth}`)
        .then(response => response.json())
        .then(data => {
            console.log("Filtered Data:", data);
            document.getElementById("totalCases").textContent = data.total_cases;
            document.getElementById("criminalCases").textContent = data.criminal_cases;
            document.getElementById("civilCases").textContent = data.civil_cases;

            updateBarChart(data.civil_cases, data.criminal_cases);
            updateLineChart(data.monthly_cases);

        })
        .catch(error => console.error("Error fetching filtered data:", error));
}

// Re-fetch data on filter change
startYearFilter.addEventListener("change", fetchDashboardData);
startMonthFilter.addEventListener("change", fetchDashboardData);
endYearFilter.addEventListener("change", fetchDashboardData);
endMonthFilter.addEventListener("change", fetchDashboardData);

// Update Bar Chart
let barChartInstance;
function updateBarChart(civilCases, criminalCases) {
    if (barChartInstance) {
        barChartInstance.destroy();
    }

    const barChartCtx = document.getElementById('pieChart').getContext('2d');
    barChartInstance = new Chart(barChartCtx, {
        type: 'bar',
        data: {
            labels: ['Criminal Cases', 'Civil Cases'],
            datasets: [{
                label: 'Cases Filed',
                data: [criminalCases, civilCases],
                backgroundColor: ['rgb(0, 67, 200)', 'rgb(3, 29, 82)'],
                borderColor: ['white', 'white'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

// Update Line Chart
let lineChartInstance;
function updateLineChart(monthlyCases) {
    if (lineChartInstance) {
        lineChartInstance.destroy();
    }

    const startYear = parseInt(startYearFilter.value);
    const startMonth = parseInt(startMonthFilter.value);
    const endYear = parseInt(endYearFilter.value);
    const endMonth = parseInt(endMonthFilter.value);

    const labels = [];
    const civilData = [];
    const criminalData = [];

    let current = new Date(startYear, startMonth - 1);
    const end = new Date(endYear, endMonth - 1);

    while (current <= end) {
        const yearMonth = current.toISOString().slice(0, 7); // "YYYY-MM"
        labels.push(`${monthNames[current.getMonth()]} ${current.getFullYear()}`);

        const caseData = monthlyCases[yearMonth] || { civil: 0, criminal: 0 };
        civilData.push(caseData.civil);
        criminalData.push(caseData.criminal);

        // Move to next month
        current.setMonth(current.getMonth() + 1);
    }

    const ctx = document.getElementById('lineChart').getContext('2d');
    lineChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Criminal Cases',
                    data: criminalData,
                    borderColor: 'rgb(0, 67, 200)',
                    backgroundColor: 'rgba(1, 17, 105, 0.2)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Civil Cases',
                    data: civilData,
                    borderColor: 'rgb(3, 29, 82)',
                    backgroundColor: 'rgba(5, 37, 219, 0.2)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });
}



// Logout
function redirectToAuthorization(event) {
    event.preventDefault();
    window.location.href = "configs/logout.php";
}

// Optional utility for random colors
function getRandomColor() {
    return `hsl(${Math.floor(Math.random() * 360)}, 70%, 50%)`;
}
function toggleFilter() {
            const filterIcon = document.getElementById('filterIcon');
            const filterContainer = document.querySelector('.filter-container');

            // Toggle visibility of filter container
            if (filterContainer.style.display === "none" || filterContainer.style.display === "") {
                filterContainer.style.display = "flex"; // Show the filter
                filterIcon.innerHTML = '<i class="fas fa-times"></i>'; // Change to X icon
            } else {
                filterContainer.style.display = "none"; // Hide the filter
                filterIcon.innerHTML = '<i class="fas fa-filter"></i>'; // Change back to filter icon
            }
            filterIcon.classList.toggle('active');
        }


        </script>
    </body>
    </html>