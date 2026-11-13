<?php

namespace BlueSpice\DistributionConnector;

use MediaWiki\Config\Config;
use MediaWiki\Html\Html;
use MediaWiki\Message\Message;
use MediaWiki\User\User;
use Wikimedia\Rdbms\ILoadBalancer;

class UserCounter {

	/** @var array */
	private $userLimits = [
		'4cb76833e743e9313ec7f0adb371c0fdbacf57ed' => 25,
		'9d3cc888b83410c13b1406f784fb55129c36b520' => 50,
		'a416fa165396d208d806b63ba409b86e23c2cb10' => 100,
		'7c3e5945d5e71bbdc17fda125e3e4d2591916e87' => 150,
		'330f0c275d554b3baac829758443511650b69dcf' => 200,
		'9d30f6794f1ee6bc3d8d4e7495aff9e6ffb12d75' => 250,
		'ec8da2fd187588cbc4a176d8fc831705e4c799e5' => 300,
		'7bb43796c1ac9456225c128c206f7455efd362e7' => 400,
		'88530acf24588b4b8ad0cae873ab22d19f77f959' => 500,
		'c9f9f6b1090aa7b4174443d295fe50e47a0b04c7' => 600,
		'10c0fa889b942b27368af58687b687f256e2665b' => 800,
		'20942e9dc9d1eed4c73f90add45f650b64d8f5e2' => 1000,
		'05a4e4fa3f8e6f0d01ddefa6d829daae7a3331eb' => 1200,
		'4d6279baaa388d34ff6e8b9abbb98b2357d69ca5' => 1400,
		'24b634bc4a6dccca835a5097e5147640870d611e' => 1600,
		'1686fff4675b28ac7bd8de086393b462d40880ae' => 1800,
		'c9ece401393ea5533037675658c4299826a02859' => 2000,
		'af9351be0196b7f0f0a68439a9cb7b90b91d97c9' => 2250,
		'2d5eddf952cfebd4966242c464acb3842f6f648c' => 2500,
		'ff948b41e7ae1dc2cb16795807e8c3d0eab48dd0' => 2750,
		'7a228aa99896c941169fd0074be0f6b4ba50aedc' => 3000,
		'1e27dd541e89b118eb391f8de16841d6beed0061' => 3250,
		'90873ce1d09615ed464a375be09f6a0550b9ee36' => 3500,
		'3b4a6f1d25d7542670e08962eebb05c2772d1012' => 3750,
		'b54b1f999f62f890a0b4d13042411fab5b8a20bc' => 4000,
		'd88506b8b634ee906e4ba1fb4517115e106b1830' => 4250,
		'097a1f302cb9a2758bc844b273c87b9cec4eb9e2' => 4500,
		'6219cce2cec631fa854dd006acd5e6250c888866' => 4750,
		'c0e5ea7b86dde728f5d33de195210bbad57d9b71' => 5000,
		'8514ab28bc7dfeb479dbfa0e6ee50088de73f786' => 5500,
		'7e1f975c849db45eae53df3ce167c74b0848cc93' => 6000,
		'7aa11657de8cb604b7c41c04ffe733f204517acd' => 6500,
		'7a6cbf452c41a0dce14b35710f9bc270488010bc' => 7000,
		'135b23d727f497c4d7643e348c56a078764132af' => 7500,
		'468e79ea1fe5ffe08e5087f33b86e0faa0083b1f' => 8000,
		'92410f868cc92ffeaf46fc20476317b97d0d897f' => 8500,
		'c72964d581bd861753821282c94c94170cf04325' => 9000,
		'21094e77e74e9c734338e8a69cfe5ccd6c4014ac' => 9500,
		'0a6d3a57ad1565dbd78f1126df3d315932fd5489' => 10000,
		'43fb790133c166c7eff59398c15890a8c78b174d' => 11000,
		'c83285a57cc84fb59a90453a0e4be427e8e302cf' => 12000,
		'26916b721289adff4a7df2735df0d364d31dfe3b' => 13000,
		'0ccf8def8015f22ac35fff233857207d1ef00217' => 14000,
		'95f00573754a9f9dd9b59cccc432be9181807448' => 15000,
		'e6a86a5f28f8f9a4e7ef062d2571e18b3322f7ef' => 16000,
		'ac259ff3cac6fd1ee7826c7de67409302dd9cdde' => 17000,
		'ea1e7052a0683ad864decb3b657ed1226f7d6da1' => 18000,
		'6dcdd6e9a5110e3f7047c30d9c871b02702cdea7' => 19000,
		'0aa4346c1c5ca8bb6818a94982466a029d35fdac' => 20000,
		'676bfe78bd64eab911c4eef020836749c2cbeb3d' => 21000,
		'ce1db2e186ac96db5ddf988f11d43bfbbd0eda8f' => 22000,
		'ac90c57e01220c34be4cfb604e9578e2f129056f' => 23000,
		'80df5580e5c9c98a1117a1f8c7f4fab14402f5fa' => 24000,
		'9446000de63e2f5868e9c87eb5d2d090a7b81480' => 25000,
		'57fe291fca575743fa841cbfdb19e0f77afb9474' => 26000,
		'06b66493df5ef34526a6fa90c9dc82bfe927a430' => 27000,
		'5cc04ae62851d9930142356a92910056661ad77e' => 28000,
		'69e743cca06ff557dc399fdec3a529e1ce01b047' => 29000,
		'126748f88b03f7c20250b0ebd1c035ca13f493be' => 30000,
		'950aeae7fa19cdfce73cd7bd4742af3339dc64ed' => 31000,
		'df0c5371b346d17def980d918256b97f0e5e51a8' => 32000,
		'09974bf714e29fba7e99a1f6aca8fa378536455f' => 33000,
		'c6119758d3d69eb184a7822be51e11ae47928e30' => 34000,
		'79ed98bc3b5ba83cbebbcf545b4bd24c23091935' => 35000,
		'2733ca3308577d8f63b05c25d0769acaac795c8e' => 36000,
		'fed0f794606bfe954813d8954aab2531069684ee' => 37000,
		'6a9abe51aeaf378cdc39060f8d69a3ba636c37d4' => 38000,
		'38f3c1c11fd2e17f1102d6ee55d0c16d198119ad' => 39000,
		'42c6e6df5e8849443deb6c3f210266c133535dc6' => 40000,
		'84ae1b0712da605d602d9d09a8e2a69ccb3d5a01' => 41000,
		'f282966b7e410da8f161a9f77ffbe9ab1103797e' => 42000,
		'5d921c26e6abd27263ead84dafc7728dc598e5fe' => 43000,
		'2a4375243d4e9ad779c304577d8b3b1524e9bd7e' => 44000,
		'9574c2599ba07ec983aeb53d02f819221cd46752' => 45000,
		'48450942b5642eea5ee89d4aa4ebdfba661422c6' => 46000,
		'a1a50df90fc8e886c7ac89e164b3aa05552d8d86' => 47000,
		'b471c6a2213c1072c6800f8e9a526117fd51460b' => 48000,
		'f5ca02a15c37975750f3927553b29f257ae784a9' => 49000,
		// Unlimited
		'5185bf3320c8930f2cd4b7bbbe500b7151ead181' => -1,

		// ERM48083
		'238fb3cbacc4f16ffe05440ed204934ae2ab216f' => 10,
		'c05b21e5c19826779721a7b5968f23965dc59d48' => 20,
		'f0dc95eccf5b0dd52e7557ce2d34911e188fe752' => 25,
		'07e33531f5a34bac94279a8b72e6101a5cd8cb1c' => 30,
		'e955848016436d2765f2cb03e7a3c6e6142d2e6c' => 40,
		'da5cc954c7a7c9a858984f87d2b05962e42b4796' => 50,
		'74fd4fbee57d97759a53374d7587983e6f0ef8d4' => 60,
		'0850ca56e4030772535dd1811bf884b69b7f9640' => 70,
		'05ea74e2c1372a94a03dd746896ece7eca4c56c3' => 80,
		'fe468d730bebe11aa6e26505bf1c3b8a6190656c' => 90,
		'113133f730b0dc8f93825ec0bc192af2e9716db6' => 100,
		'f214d840deef689fcda89f50d5da8c96988e5ac4' => 150,
		'01b18e79621670c5d46f9e3eeff7bac21106bf51' => 200,
		'0ccb2fd6b0f449c03fb18ca9ef6e152504f2ba1f' => 250,
		'6b592d889370712332aee8145bdc272ba2ba6f96' => 300,
		'fc9fb4fc294116d94ebac44741d0228e288772cb' => 350,
		'9968130efa5975dbee15726a1592ed1f417e3bab' => 400,
		'f7ced41c1012ec72babcdaaf657cab8ca7d55077' => 450,
		'd2d59afbe106385eebf0ca2344bb5bfe71608e12' => 500,
	];

	/** @var Config */
	protected $config;
	/** @var Config */
	protected $mainConfig;
	/** @var ILoadBalancer */
	protected $lb;

	/** @var EditionProvider */
	private $editionProvider;

	/** @var string[] */
	private $warningLevels = [
		70 => 'orange',
		90 => 'red'
	];

	/**
	 * @param Config $config
	 * @param Config $mainConfig
	 * @param ILoadBalancer $lb
	 * @param EditionProvider $editionProvider
	 */
	public function __construct(
		Config $config, Config $mainConfig, ILoadBalancer $lb, EditionProvider $editionProvider
	) {
		$this->config = $config;
		$this->mainConfig = $mainConfig;
		$this->lb = $lb;
		$this->editionProvider = $editionProvider;
	}

	/**
	 * Get the maximum allowed active user from the edition or the subscription key
	 *
	 * @return int `-1` if unlimited
	 */
	public function getUserLimit() {
		$editionUserLimit = $this->editionProvider->getEditionUserLimit();
		if ( $editionUserLimit !== null ) {
			return $editionUserLimit;
		}
		if ( !$this->editionProvider->checkRequiresSubscriptionKey() ) {
			return -1;
		}
		$subscriptionKey = $this->normalizeSubscriptionKey( (string)$this->config->get( 'SubscriptionKey' ) );
		if ( !$subscriptionKey || !isset( $this->userLimits[$subscriptionKey] ) ) {
			return array_values( $this->userLimits )[0];
		}
		return $this->userLimits[$subscriptionKey];
	}

	/**
	 * Get the current number of active user.
	 * Bots and an additional whitelist are ignored.
	 *
	 * @return int
	 */
	public function getCurrentNumberOfUser() {
		$whitelist = $this->getUserNameWhitelist();
		$userCount = 0;

		$dbr = $this->lb->getConnection( DB_REPLICA );
		$result = $dbr->select(
			'user',
			[ '*' ],
			[ 'user_name NOT IN (' . $dbr->makeList( $whitelist ) . ')' ],
			__METHOD__
		);

		if ( !$result ) {
			return $userCount;
		}

		foreach ( $result as $row ) {
			$user = User::newFromRow( $row );
			$block = $user->getBlock();

			if ( $block === null ) {
				$userCount++;
			}
		}

		return (int)$userCount;
	}

	/**
	 * Get the formatted status sentence as HTML
	 * @return string
	 */
	public function getSentenceHtml() {
		return $this->getSentenceInternal( true );
	}

	/**
	 * Get status sentence
	 * @return string
	 */
	public function getSentence() {
		return $this->getSentenceInternal( false );
	}

	/**
	 * Get ratio of usage
	 *
	 * @param int|null $currentCount
	 * @param int|null $limit
	 * @return int|null
	 */
	public function getRatio( ?int $currentCount = null, ?int $limit = null ) {
		if ( $currentCount === null ) {
			$currentCount = $this->getCurrentNumberOfUser();
		}
		if ( $limit === null ) {
			$limit = $this->getUserLimit();
			if ( $limit < 1 ) {
				return null;
			}
		}
		return intval( ( $currentCount * 100 ) / $limit );
	}

	/**
	 * @param bool|null $html
	 * @return string
	 */
	protected function getSentenceInternal( $html = false ) {
		$currentCount = $this->getCurrentNumberOfUser();
		$limit = $this->getUserLimit();
		$percent = null;

		if ( $limit === 0 ) {
			$status = Message::newFromKey(
				'bs-pro-distribution-instance-status-number-of-users-unknown'
			)->params( $currentCount )->parse();
		} elseif ( $limit === -1 ) {
			$status = Message::newFromKey(
				'bs-pro-distribution-instance-status-number-of-users-unlimited'
			)->params( $currentCount )->parse();
		} else {
			$percent = $this->getRatio( $currentCount, $limit );
			if ( $percent > 99 ) {
				$status = Message::newFromKey(
					'bs-pro-distribution-instance-status-number-of-users-limited-full'
				)->params( $currentCount )->parse();
			} else {
				$status = Message::newFromKey(
					'bs-pro-distribution-instance-status-number-of-users-limited'
				)->params( $currentCount, $limit, $percent )->parse();
			}
		}

		if ( !$html ) {
			return $status;
		}

		if ( $percent !== null ) {
			$color = 'green';
			foreach ( $this->warningLevels as $level => $levelColor ) {
				if ( $level <= $percent ) {
					$color = $levelColor;
				}
			}

			$html = Html::openElement( 'span', [
				'style' => "color: $color",
			] );
		} else {
			$html = Html::openElement( 'span' );
		}

		$html .= $status;
		$html .= Html::closeElement( 'span' );

		return $html;
	}

	/**
	 * Get a whitelist with user which are not counted for the limit
	 *
	 * @return array
	 */
	private function getUserNameWhitelist() {
		return array_merge(
			$this->mainConfig->get( 'ReservedUsernames' ) ?? [],
			$this->config->get( 'UserLimitWhitelist' ) ?? []
		);
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	private function normalizeSubscriptionKey( string $key ): string {
		if ( !$key ) {
			return '';
		}
		$key = trim( strtolower( str_replace( '-', '', $key ) ) );
		return sha1( $key );
	}
}
