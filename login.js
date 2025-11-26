const express = require('express');
const bodyParser = require('body-parser');
const bcrypt = require('bcryptjs');
const session = require('express-session');
const fs = require('fs');

const app = express();
const PORT = 3000;

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(session({ secret: 'mysecret', resave: false, saveUninitialized: true }));
app.use(express.static('public')); // Serve static files from /public

// Simple user storage
const USERS_FILE = 'users.json';
if (!fs.existsSync(USERS_FILE)) fs.writeFileSync(USERS_FILE, JSON.stringify([]));

// Routes

// Home page → index.html
app.get('/', (req, res) => {
    res.sendFile(__dirname + '/public/index.html');
});

// Login page → simple-login.html
app.get('/login', (req, res) => {
    res.sendFile(__dirname + '/public/simple-login.html');
});

// Registration route
app.post('/register', (req, res) => {
    const { username, password } = req.body;
    const users = JSON.parse(fs.readFileSync(USERS_FILE));

    if (users.find(u => u.username === username)) return res.send('Username already exists.');

    const hashedPassword = bcrypt.hashSync(password, 8);
    users.push({ username, password: hashedPassword });
    fs.writeFileSync(USERS_FILE, JSON.stringify(users));

    res.send('Registration successful! <a href="/login">Login here</a>');
});

// Login route
app.post('/login', (req, res) => {
    const { username, password } = req.body;
    const users = JSON.parse(fs.readFileSync(USERS_FILE));
    const user = users.find(u => u.username === username);

    if (!user) return res.send('User not found.');
    if (!bcrypt.compareSync(password, user.password)) return res.send('Wrong password.');

    req.session.user = { username: user.username };
    res.redirect('/dashboard');
});

// Dashboard page (after login)
app.get('/dashboard', (req, res) => {
    if (!req.session.user) return res.redirect('/login');
    res.send(`<h1>Welcome, ${req.session.user.username}!</h1><a href="/logout">Logout</a>`);
});

// Logout
app.get('/logout', (req, res) => {
    req.session.destroy();
    res.redirect('/login');
});

// Start server
app.listen(PORT, () => console.log(`Server running on http://localhost:${PORT}`));
