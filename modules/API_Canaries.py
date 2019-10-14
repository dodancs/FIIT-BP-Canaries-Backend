from flask import jsonify

def API_Canaries(app):
    @app.route('/canaries', methods=['GET'])
    def canries():
        return jsonify({})

    @app.route('/canaries/<id>/get_details', methods=['GET'])
    @app.route('/canaries/<id>/get_details/<attribute>', methods=['GET'])
    def canries_get_details(id, attribute=None):
        return jsonify({'id' : id, 'attribute': attribute})

    @app.route('/canaries/<id>/get_mail', methods=['GET'])
    def canries_get_mail(id):
        return jsonify({'id' : id})