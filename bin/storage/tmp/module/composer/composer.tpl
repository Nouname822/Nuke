{
    "name": "nuke/{{ name }}",
    "description": "",
    "type": "library",
    "require": {},
    "autoload": {
        "psr-4": {
            "{{ namespace }}\\Controllers\\": "Controllers/",
            "{{ namespace }}\\Dto\\": "Dto/",
            "{{ namespace }}\\Enums\\": "Enums/",
            "{{ namespace }}\\Interfaces\\": "Interfaces/",
            "{{ namespace }}\\Middlewares\\": "Middlewares/",
            "{{ namespace }}\\Services\\": "Services/",
            "{{ namespace }}\\Models\\": "Models/"
        }
    },
    "license": "MIT",
    "authors": [
        {
            "name": "Nouname822",
            "email": "Nouname822@gmail.com"
        }
    ],
    "minimum-stability": "stable",
    "prefer-stable": true
}