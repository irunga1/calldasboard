    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (!empty($scripts)) foreach ((array)$scripts as $src): ?>
    <script type="module" src="<?= htmlspecialchars($src) ?>"></script>
    <?php endforeach; ?>
</body>
</html>
