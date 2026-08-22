<script src="<?= $assetBase ?>assets/js/main.js"></script>
<?php if (!empty($extraScripts)): foreach ($extraScripts as $s): ?>
<script src="<?= $assetBase . $s ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
