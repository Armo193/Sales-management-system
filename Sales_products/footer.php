</main>

<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            
            <div class="footer-section">
                <h2>Back home.</h2>
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Support</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-question-circle"></i> Help Center</a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> Contact Us</a></li>
                    
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Planet Victoria Sales Management System. All rights reserved.</p>
            <div class="system-info">
                <span>v1.0.0</span>
                <span id="clock"></span>
            </div>
        </div>
    </div>
</footer>

<script>
    // Real-time clock
    function updateClock() {
        const now = new Date();
        const clock = document.getElementById('clock');
        clock.textContent = now.toLocaleTimeString();
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Main JS file -->
<script src="../assets/js/main.js"></script>
</body>
</html>