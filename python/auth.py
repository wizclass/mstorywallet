from oauth2client.service_account import ServiceAccountCredentials
from flask_api import status
from flask import make_response, jsonify
import datetime
from app import create_app 
from flask_restx import Api,Resource


app = create_app()
api = Api(app)

google_access_token_vaild_time = 0
google_access_token = None

@api.route('/fcm-token')
class generate_fcm_token(Resource):
    def get(self):
        global google_access_token_vaild_time, google_access_token

        code = status.HTTP_200_OK
        now = datetime.datetime.today().timestamp()

        if(google_access_token_vaild_time <= now):
            
            credentials = ServiceAccountCredentials.from_json_keyfile_name('./service-account.json', ['https://www.googleapis.com/auth/firebase.messaging'])
            access_token_info = credentials.get_access_token()
            google_access_token = access_token_info[0] if access_token_info else None
            google_access_token_vaild_time = now + access_token_info[1] if access_token_info else 0
            
        else:
            
            google_access_token = google_access_token


        token = {"token" : google_access_token}

        return make_response(jsonify(
            code = code,
            contents = token
        ),code) 
        
if __name__ == "__main__": 
    app.run(debug=True)