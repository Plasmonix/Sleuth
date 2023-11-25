import re
import os
import requests
import subprocess

api_endpoint = 'https://your-website.com/auth.php'

def validate_license_key(key):
    pattern = re.compile(r'^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$')
    return bool(pattern.match(key))

try:
    with open('key.txt', 'r') as file:
        license_key = file.read().strip()
        if not validate_license_key(license_key):
            raise ValueError('Invalid license key format')
except (FileNotFoundError, ValueError):
    while True:
        license_key = input('Enter your license key: ')
        if validate_license_key(license_key):
            with open('key.txt', 'w') as file:
                file.write(license_key)
            break
        else:
            print('Invalid license key format. Please try again.')


ip_address_response = requests.get('https://api.ipify.org/')
ip_address = ip_address_response.text if ip_address_response.status_code == 200 else 'N/A'

try:
    hwid = str(subprocess.check_output('wmic csproduct get uuid')).split('\\r\\n')[1].strip('\\r').strip()
except subprocess.CalledProcessError:
    hwid = 'N/A'

username = os.getlogin()

def get_user_data():
    user_info_data = {
        'license': license_key,
        'action': 'getUserInfo'
    }

    user_info_response = requests.post(api_endpoint, data=user_info_data)
    data = user_info_response.json()

    if data['status'] == 'success':
        print("User Information:")
        print(f"Name: {data['data']['name']}")
        print(f"Expiry: {data['data']['expiry']}")
        print(f"Status: {data['data']['status']}")
    else:
        print(f"User not found or error: {user_info_response.status_code} - {user_info_response.text}")
        register_user()
    
def register_user():
    user_data = {
    'name': username,
    'role': 'user',
    'ip': ip_address,
    'hwid': hwid,
    'license': license_key,
    'action': 'registerUser'
    }

    register_user_response = requests.post(api_endpoint, data=user_data)

    if register_user_response.status_code == 200:
        register_result = register_user_response.json()

        if register_result['status'] == 'success':
            with open('key.txt', 'w') as file:
                file.write(license_key)

            print("User registered successfully.")
            get_user_data()
        else:
            print(f"Error during registration: {register_result['status']} - {register_result['message']}")
    else:
        print(f"Error during registration: {register_user_response.status_code} - {register_user_response.text}")

if __name__ == '__main__':
    get_user_data()