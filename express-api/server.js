const express = require('express');
const app = express();
const PORT = 3000;
app.use(express.json());

app.get('/api/message', (req, res) => {
  res.json({ success: true, message: 'Hello from Express REST API!' });
});

// Express calls the PHP REST API in the parent project.
app.get('/call-php-api', async (req, res) => {
  try {
    const response = await fetch('http://localhost/php_practical_assignment/api/products.php');
    const data = await response.json();
    res.json({ called: 'PHP REST API', result: data });
  } catch (error) {
    res.status(500).json({ error: 'Could not call PHP API. Check WAMP and project folder name.' });
  }
});

app.listen(PORT, () => console.log(`Express API running at http://localhost:${PORT}`));
