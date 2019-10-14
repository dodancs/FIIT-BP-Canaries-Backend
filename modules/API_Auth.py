from flask import jsonify

def API_Auth(app):
    @app.route('/auth', methods=['GET'])
    def auth():
        return jsonify({})

    @app.route('/auth/login', methods=['GET'])
    def auth_login():
        return jsonify({})

    @app.route('/auth/logout', methods=['GET'])
    def auth_logout():
        return jsonify({})

    @app.route('/auth/users', methods=['GET'])
    def auth_users():
        return jsonify({})