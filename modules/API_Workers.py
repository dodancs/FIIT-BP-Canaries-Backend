from flask import jsonify

def API_Workers(app):
    @app.route('/workers', methods=['GET'])
    def workers():
        return jsonify({})