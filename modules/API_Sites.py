from flask import jsonify

def API_Sites(app):
    @app.route('/sites', methods=['GET'])
    def sites():
        return jsonify({})