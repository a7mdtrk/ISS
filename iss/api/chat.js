const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const CryptoJS = require('crypto-js'); // For encryption

const app = express();
const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

const SECRET_KEY = 'supersecretkey'; // Symmetric encryption key
const rooms = {
    encrypted: [],
    unencrypted: []
};

// WebSocket connection handler
wss.on('connection', (ws, req) => {
    console.log('New client connected');

    // Handle incoming messages
    ws.on('message', (data) => {
        const { room, message, encrypted } = JSON.parse(data);

        if (room === 'encrypted') {
            // Encrypt the message before broadcasting
            const encryptedMessage = CryptoJS.AES.encrypt(message, SECRET_KEY).toString();
            rooms.encrypted.forEach(client => {
                if (client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify({ encrypted: true, message: encryptedMessage }));
                }
            });
        } else if (room === 'unencrypted') {
            // Broadcast plaintext message
            rooms.unencrypted.forEach(client => {
                if (client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify({ encrypted: false, message }));
                }
            });
        }
    });

    // Add client to a room
    ws.on('message', (data) => {
        const { room } = JSON.parse(data);
        if (room === 'encrypted') {
            rooms.encrypted.push(ws);
        } else if (room === 'unencrypted') {
            rooms.unencrypted.push(ws);
        }
    });

    // Remove client on disconnect
    ws.on('close', () => {
        console.log('Client disconnected');
        rooms.encrypted = rooms.encrypted.filter(client => client !== ws);
        rooms.unencrypted = rooms.unencrypted.filter(client => client !== ws);
    });
});

server.listen(3000, () => {
    console.log('Server is running on http://localhost:3000');
});
