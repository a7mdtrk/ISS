const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const mysql = require('mysql2');
const CryptoJS = require('crypto-js');

const app = express();
const server = http.createServer(app);

// Enable CORS for Socket.io
const io = socketIo(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});

// MySQL Database Connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'iss'
});

db.connect(err => {
    if (err) {
        console.error('Database connection failed:', err.message);
        process.exit(1);
    }
    console.log('Connected to MySQL database.');
});

const SECRET_KEY = "supersecretkey";

// Simplified DES encryption function
function desEncrypt(plainText, key) {
    return CryptoJS.DES.encrypt(plainText, key).toString();
}

// DES decryption
function desDecrypt(encryptedText, key) {
    return CryptoJS.DES.decrypt(encryptedText, key).toString(CryptoJS.enc.Utf8);
}

// Socket.io connection
io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    socket.on('message', (msg) => {
        const { sender_id, receiver_id, room_id, message_text, encryption_type } = msg;

        let encryptedMessage = null;

        if (encryption_type === 'aes') {
            encryptedMessage = CryptoJS.AES.encrypt(message_text, SECRET_KEY).toString();
        } else if (encryption_type === 'des') {
            encryptedMessage = desEncrypt(message_text, SECRET_KEY);
        }

        const sql = `INSERT INTO chat_messages (sender_id, receiver_id, room_id, message_text, encrypted_message) 
                     VALUES (?, ?, ?, ?, ?)`;

        db.query(sql, [sender_id, receiver_id, room_id, message_text, encryptedMessage || ''], (err) => {
            if (err) {
                console.error('Error saving message to DB:', err.message);
                return;
            }
            console.log(`Message saved: ${message_text}, Encrypted: ${encryptedMessage || 'N/A'}`);

            // Broadcast the message
            io.to(room_id).emit('message', {
                room_id,
                plaintext_message: message_text,
                encrypted_message: encryptedMessage || null,
                encryption_type
            });
        });
    });

    socket.on('joinRoom', (room_id) => {
        socket.join(room_id);
        console.log(`User ${socket.id} joined room ${room_id}`);
    });

    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});

server.listen(3000, () => {
    console.log('Chat server running on http://localhost:3000');
});
