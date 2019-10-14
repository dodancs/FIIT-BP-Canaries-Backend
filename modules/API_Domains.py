from flask import jsonify

def API_Domains(app):
    @app.route('/domains', methods=['GET'])
    def domains():
        return jsonify({})