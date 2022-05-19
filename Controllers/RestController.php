<?php
namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Validator\Constraints as Assert;
use Nette\Database\Connection;

class RestController
{
	/**
	 * Database variable
	 *
	 * @var Nette\Database\Connection;
	 */
	private $db;

	public function __construct()
	{
		$this->db = new Connection(DB_HOST, DB_USERNAME, DB_PASSWORD);
	}
	
	/**
	 * Store data to database
	 *
	 * @return void
	 */
	public function post()
	{
		$validator = Validation::createValidator();

		$groups = new Assert\GroupSequence(['Default', 'custom']);

		$constraint = new Assert\Collection([
			'email' => new Assert\Email(),
			'phone' => new Assert\Regex([
				'pattern' => '/^\+?\d[ -]?\(?\d{3}\)?[ -]?\d{3}[ -]?\d{2}[ -]?\d{2}$/',
			]),
			'message' => new Assert\Optional()
		]);

		$request = Request::createFromGlobals();

		$violations = $validator->validate($request->request->all(), $constraint, $groups);

		$result = ['status' => 'success'];

		if (0 !== count($violations)) {
			$result['status'] = 'error';
			foreach ($violations as $violation) {
				$result['messages'][] = [
					'property' => $violation->getPropertyPath(),
					'value'    => $violation->getInvalidValue(),
					'message'  => $violation->getMessage()
				];
			}
		} else {
			// prevent xss attack!
			$message = htmlspecialchars($request->request->get('message'));

			$this->db->query('INSERT INTO ' . DB_TABLE, [
				'email'   => $request->request->get('email'),
				'phone'   => $request->request->get('phone'),
				'message' => $message,
			]);
		}

		$response = new JsonResponse();
		$response->setContent(json_encode($result));
		return $response->send();
	}

	/**
	 * Get data as json
	 *
	 * @return string
	 */
	public function get()
	{
		$context = new RequestContext();
		$request = Request::createFromGlobals();
		$context->fromRequest($request);
		$response = new JsonResponse();
		$usersData = $this->db->fetchAll('SELECT * FROM ' . DB_TABLE);
		$response->setContent(json_encode($usersData));
		return $response->send();
	}

	/**
	 * For testing
	 *
	 * @return string
	 */
	public function test()
	{
		?>
		<form action="/" method="post" style="padding: 30px;width: 300px">
			<label>Email:<br>
				<input type="text" name="email" style="width: 100%">
			</label><br><br>
			<label>Phone:<br>
				<input type="text" name="phone" style="width: 100%">
			</label><br><br>
			<label>Message:<br>
				<textarea name="message" cols="30" rows="10" style="width: 100%"></textarea>
			</label><br><br>
			<button type="submit" value="" style="margin: 0 auto;display:block">SUBMIT</button>
		</form>
		<table border="1" cellpadding="5" style="margin: 30px;">
			<thead>
				<tr>
					<th>ID</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Message</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$items = $this->db->fetchAll('SELECT * FROM ' . DB_TABLE);
					foreach($items as $item) {
				?>
					<tr>
						<td><?php echo $item->id ?></td>
						<td><?php echo $item->email ?></td>
						<td><?php echo $item->phone ?></td>
						<td><?php echo $item->message ?></td>
					</tr>
				<?php
					}
				?>
			</tbody>
		</table>
		<?php
	}
}