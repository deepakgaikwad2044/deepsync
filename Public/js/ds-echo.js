class DSEcho {
    constructor(url, token) {
        this.url = url;
        this.token = token;
        this.socket = null;
        this.channels = {};
        this.connect();
    }

    connect() {
        this.socket = new WebSocket(this.url);

        this.socket.onopen = () => {
            console.log("✅ WebSocket Connected");

            // Subscribe all pending channels
            Object.keys(this.channels).forEach(channel => {
                this._sendSubscribe(channel);
            });
        };

        this.socket.onmessage = event => {
            const response = JSON.parse(event.data);

            if (this.channels[response.channel]) {
                this.channels[response.channel](response.data);
            }
        };

        this.socket.onclose = () => {
            console.log("⚠️ WebSocket Disconnected. Reconnecting...");
            setTimeout(() => this.connect(), 2000);
        };
    }

    subscribe(channel, callback) {
        this.channels[channel] = callback;

        // If socket already open → subscribe immediately
        if (this.socket.readyState === WebSocket.OPEN) {
            this._sendSubscribe(channel);
        }
    }

    _sendSubscribe(channel) {
        this.socket.send(
            JSON.stringify({
                action: "subscribe",
                channel: channel,
                token: this.token
            })
        );
    }
}
