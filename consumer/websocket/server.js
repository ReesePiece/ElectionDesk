var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var CBuffer = require('CBuffer');

server.listen(4242);

app.get('/', function (req, res) {
    res.redirect('http://electiondesk.us');
});

io.on('connection', function (socket) {
    socket.on('dump', function (data) {
        console.log('Sending data dump');
        var items = buffer.toArray();
        items.reverse(); // We want newest first

        items.forEach(function (item) {
            socket.emit('update', item);
        });
    });

    socket.emit('hello');
});

// Load the TCP Library
var net = require('net'),
    carrier = require('carrier');

// Save last 250 items
var buffer = CBuffer(250);

// Start a TCP Server
net.createServer(function (connection) {
    carrier.carry(connection, function(line) {
        buffer.push(line);

        //try {
            console.log('Sending interaction');
            io.sockets.emit('update', line);
        //} catch (e) {
        //    console.log('Could not send', line);
        //}
    });
}).listen(4244);