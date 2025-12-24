import requests
import json
import sys

# Test the test-translate endpoint
url = "http://localhost:8000/test-translate?lang=en"

try:
    print("Testing translation service endpoint...")
    print(f"URL: {url}\n")

    response = requests.get(url, timeout=5)
    print(f"Status: {response.status_code}")
    print(f"Headers: {dict(response.headers)}\n")

    try:
        data = response.json()
        print("Response JSON:")
        print(json.dumps(data, indent=2))
    except:
        print("Response Text:")
        print(response.text[:500])

except Exception as e:
    print(f"Error: {e}")
    print("\nMake sure Laravel is running: php artisan serve")
    sys.exit(1)
