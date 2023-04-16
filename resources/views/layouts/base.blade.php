<!DOCTYPE html>
<html>
<head>
	<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Mon site web</title>
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<style>
		/* Navbar styling */
		header {
			background-color: #fff;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			z-index: 1;
		}

		nav ul {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 1rem;
			margin: 0;
		}

		nav ul li {
			list-style: none;
			font-size: 1.2rem;
		}

		nav ul li a {
			color: #007bff;
			text-decoration: none;
			padding: 0.5rem 1rem;
			border-radius: 0.5rem;
			transition: background-color 0.3s ease;
		}

		nav ul li a:hover {
			background-color: #007bff;
			color: #fff;
		}

		/* Footer styling */
		footer {
			background-color: #007bff;
			color: #fff;
			padding: 1rem;
			position: fixed;
			bottom: 0;
			left: 0;
			width: 100%;
		}

			footer p {
				margin: 0;
				font-size: 1rem;
			}
			/* Style des formulaires */

	form {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		margin: 2rem 0;
		padding: 2rem;
		border-radius: 1rem;
		box-shadow: 0 0 1rem rgba(0, 0, 0, 0.2);
		background-color: #fff;
		width: 100%;
		max-width: 600px;
		transition: transform 0.3s ease;
	}

	form:hover {
		transform: translateY(-0.5rem);
		box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
	}

	form label {
		display: block;
		margin-bottom: 1rem;
		font-weight: bold;
		font-size: 1.2rem;
	}

	form input[type="text"],
	form input[type="email"],
	form input[type="password"] {
		display: block;
		margin-bottom: 2rem;
		padding: 1rem;
		border-radius: 0.5rem;
		border: none;
		box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
		font-size: 1.2rem;
		width: 100%;
		max-width: 400px;
	}

	form button[type="submit"] {
		background-color: #007bff;
		color: #fff;
		border: none;
		border-radius: 0.5rem;
		padding: 1rem 2rem;
		font-size: 1.2rem;
		cursor: pointer;
		transition: background-color 0.3s ease;
	}

	form button[type="submit"]:hover {
		background-color: #0062cc;
	}

	/* Zoom on hover */
	.zoom {
		transition: transform 0.5s;
		transform: scale(1.1);
	}


.user-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 2rem 0;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 0 1rem rgba(0, 0, 0, 0.2);
        background-color: #fff;
        width: 100%;
        max-width: 600px;
        transition: transform 0.3s ease;
    }

    .user-info:hover {
        transform: translateY(-0.5rem);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
    }

    .user-info h2 {
        margin-bottom: 1rem;
        font-weight: bold;
        font-size: 2rem;
    }

    .user-info p {
        margin-bottom: 0.5rem;
        font-size: 1.2rem;
    }

    .user-info button {
        background-color: #dc3545;
        color: #fff;
        border: none;
        border-radius: 0.5rem;
        padding: 1rem 2rem;
        font-size: 1.2rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .user-info button:hover {
        background-color: #c82333;
    }

	nav ul li a {
    color: #007bff;
		text-decoration: none;
		padding: 0.5rem 1rem;
		border-radius: 0.5rem;
		transition: background-color 0.3s ease;
		border: 1px solid #007bff;
	}

	nav ul li a:hover {
		background-color: #007bff;
		color: #fff;
		border: 1px solid #fff;
	}

	.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 2rem 0;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 0 1rem rgba(0, 0, 0, 0.2);
    background-color: #fff;
    width: 100%;
    max-width: 800px;
    transition: transform 0.3s ease;
	}

	

	.container:hover {
		transform: translateY(-0.5rem);
		box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
	}

	h1 {
		font-size: 2rem;
		margin-bottom: 2rem;
	}

	.table {
		width: 100%;
		border-collapse: collapse;
	}

	.table thead th {
		text-align: left;
		background-color: #007bff;
		color: #fff;
		padding: 1rem;
		border: none;
	}

	.table tbody tr {
		border-bottom: 1px solid #ddd;
	}

	.table tbody td {
		padding: 1rem;
	}

	.table tbody td a {
		display: inline-block;
		margin-right: 1rem;
	}

	.boutton button {
		background-color: #dc3545;
		color: #fff;
		border: none;
		border-radius: 0.5rem;
		padding: 0.5rem 1rem;
		font-size: 1rem;
		cursor: pointer;
		transition: background-color 0.3s ease;
	}

	.boutton button:hover {
		background-color: #c82333;
		color: #fff;
	}

	.btn-success {
		background-color: #28a745;
		color: #fff;
		border: none;
		border-radius: 0.5rem;
		padding: 0.5rem 1rem;
		font-size: 1rem;
		cursor: pointer;
		transition: background-color 0.3s ease;
	}

	.btn-success:hover {
		background-color: #218838;
		color: #fff;
	}

	.boutton {
	/* annule la plupart des styles CSS par défaut */
	border: none;
	padding: 0;
	margin: 0;
	background: none;
	font-size: inherit;
	font-family: inherit;
	cursor: pointer;
	
	/* ajoute des styles de bouton */
	display: inline-block;
	text-align: center;
	text-decoration: none;
	color: inherit;
	background-color: #fff;
	border: 1px solid #ccc;
	padding: 8px 16px;
	border-radius: 4px;
	box-shadow: none;
	transition: all 0.2s ease-in-out;
	}

	/* optionnel : ajoute un effet de survol */
	.boutton:hover {
	background-color: #eee;
	border-color: #aaa;
	}
	/* /////////////////////// */
	
		.alert {
		display: flex;
		justify-content: center;
		align-items: center;
		margin: 0;
		padding: 10px;
		background-color: #f44336;
		color: white;
		font-weight: bold;
		text-align: center;
		} 

	</style>
</head>
<body>
	<header>
		<nav>
			<ul>
				<li><a href="/welcome">Acceuil</a></li>
				<li><a href="/applications">Application</a></li>
			</ul>
		</nav>

		@if ((session('message')))
			<div class="alert alert-{{ session('message')['type'] }}">
				{{ session('message')['text'] }}
			</div>
		@endif

	</header>

	<main style="margin-top: 5rem;margin-left: 25%;">
		@yield('content')
	</main>

	<footer>

	<footer>
		<p>Tous droits réservés.</p>
		<script src="{{ asset('js/app.js') }}">
			import './bootstrap';

			// Ajouter une classe CSS lorsque l'élément est survolé
			document.querySelectorAll('.input-animate').forEach(element => {
				element.addEventListener('mouseenter', function() {
					this.classList.add('zoom');
				});

				// Supprimer la classe CSS lorsque l'utilisateur quitte l'élément
				element.addEventListener('mouseleave', function() {
					this.classList.remove('zoom');
				});
			});
		</script>
	</footer>


</body>
</html>
