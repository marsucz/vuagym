<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to handle Vietnam Provinces
 *
 * @todo Arrange the orders of fields displaying in the checkout page: Country - Province - District - Address
 *
 * @author   htdat
 * @since    1.0
 *
 */
class WooViet_Provinces {

	/**
	 * Constructor: Add filters
	 */
	public function __construct() {
		add_filter( 'woocommerce_states', array( $this, 'add_provinces' ) );
		add_filter( 'woocommerce_get_country_locale', array( $this, 'edit_vn_locale' ) );
		add_filter( 'woocommerce_localisation_address_formats', array( $this, 'edit_vn_address_formats' ) );
	}

	/**
	 * Change the address format of Vietnam, add {state} (or "Province" in Vietnam)
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public function edit_vn_address_formats( $array ) {

		$array['VN'] = "{name}\n{company}\n{address_1}\n{city}\n{state}\n{country}";

		return $array;

	}

	/**
	 * Change the way displaying address fields in the checkout page when selecting Vietnam
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public function edit_vn_locale( $array ) {
		$array['VN']['state']['label']    = __( 'Province', 'woo-viet' );
		$array['VN']['state']['required'] = true;

		$array['VN']['city']['label']      = __( 'District', 'woo-viet' );
		$array['VN']['postcode']['hidden'] = true;

		return $array;
	}


	/**
	 * Add 63 provinces of Vietnam
	 *
	 * @param $states
	 *
	 * @return array
	 */
	public function add_provinces( $states ) {
		/**
		 * @source: https://vi.wikipedia.org/wiki/Tỉnh_thành_Việt_Nam and https://en.wikipedia.org/wiki/Provinces_of_Vietnam
		 */
		$states['VN'] = array(
			'HO-CHI-MINH-QUAN-1'     => __( 'Quận 1, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-2'     => __( 'Quận 2, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-3'     => __( 'Quận 3, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-4'     => __( 'Quận 4, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-5'     => __( 'Quận 5, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-6'     => __( 'Quận 6, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-7'     => __( 'Quận 7, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-8'     => __( 'Quận 8, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-9'     => __( 'Quận 9, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-10'     => __( 'Quận 10, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-11'     => __( 'Quận 11, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-12'     => __( 'Quận 12, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-BINH-TAN'     => __( 'Quận Bình Tân, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-BINH-THANH'     => __( 'Quận Bình Thạnh, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-GO-VAP'     => __( 'Quận Gò Vấp, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-PHU-NHUAN'     => __( 'Quận Phú Nhuận, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-TAN-BINH'     => __( 'Quận Tân Bình, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-TAN-PHU'     => __( 'Quận Tân Phú, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-QUAN-THU-DUC'     => __( 'Quận Thủ Đức, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-HUYEN-BINH-CHANH'     => __( 'Huyện Bình Chánh, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-HUYEN-CAN-GIO'     => __( 'Huyện Cần Giờ, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-HUYEN-CU-CHI'     => __( 'Huyện Củ Chi, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-HUYEN-HOC-MON'     => __( 'Huyện Hóc Môn, TP.Hồ Chí Minh', 'woo-viet' ),
			'HO-CHI-MINH-HUYEN-NHA-BE'     => __( 'Huyện Nhà Bè, TP.Hồ Chí Minh', 'woo-viet' ),
			'AN-GIANG'        => __( 'An Giang', 'woo-viet' ),
			'BA-RIA-VUNG-TAU' => __( 'Ba Ria - Vung Tau', 'woo-viet' ),
			'BAC-LIEU'        => __( 'Bac Lieu', 'woo-viet' ),
			'BAC-KAN'         => __( 'Bac Kan', 'woo-viet' ),
			'BAC-GIANG'       => __( 'Bac Giang', 'woo-viet' ),
			'BAC-NINH'        => __( 'Bac Ninh', 'woo-viet' ),
			'BEN-TRE'         => __( 'Ben Tre', 'woo-viet' ),
			'BINH-DUONG'      => __( 'Binh Duong', 'woo-viet' ),
			'BINH-DINH'       => __( 'Binh Dinh', 'woo-viet' ),
			'BINH-PHUOC'      => __( 'Binh Phuoc', 'woo-viet' ),
			'BINH-THUAN'      => __( 'Binh Thuan', 'woo-viet' ),
			'CA-MAU'          => __( 'Ca Mau', 'woo-viet' ),
			'CAO-BANG'        => __( 'Cao Bang', 'woo-viet' ),
			'CAN-THO'         => __( 'Can Tho', 'woo-viet' ),
			'DA-NANG'         => __( 'Da Nang', 'woo-viet' ),
			'DAK-LAK'         => __( 'Dak Lak', 'woo-viet' ),
			'DAK-NONG'        => __( 'Dak Nong', 'woo-viet' ),
			'DONG-NAI'        => __( 'Dong Nai', 'woo-viet' ),
			'DONG-THAP'       => __( 'Dong Thap', 'woo-viet' ),
			'DIEN-BIEN'       => __( 'Dien Bien', 'woo-viet' ),
			'GIA-LAI'         => __( 'Gia Lai', 'woo-viet' ),
			'HA-GIANG'        => __( 'Ha Giang', 'woo-viet' ),
			'HA-NAM'          => __( 'Ha Nam', 'woo-viet' ),
			'HA-NOI'          => __( 'Ha Noi', 'woo-viet' ),
			'HA-TINH'         => __( 'Ha Tinh', 'woo-viet' ),
			'HAI-DUONG'       => __( 'Hai Duong', 'woo-viet' ),
			'HAI-PHONG'       => __( 'Hai Phong', 'woo-viet' ),
			'HOA-BINH'        => __( 'Hoa Binh', 'woo-viet' ),
			'HAU-GIANG'       => __( 'Hau Giang', 'woo-viet' ),
			'HUNG-YEN'        => __( 'Hung Yen', 'woo-viet' ),
			'KHANH-HOA'       => __( 'Khanh Hoa', 'woo-viet' ),
			'KIEN-GIANG'      => __( 'Kien Giang', 'woo-viet' ),
			'KON-TUM'         => __( 'Kon Tum', 'woo-viet' ),
			'LAI-CHAU'        => __( 'Lai Chau', 'woo-viet' ),
			'LAO-CAI'         => __( 'Lao Cai', 'woo-viet' ),
			'LANG-SON'        => __( 'Lang Son', 'woo-viet' ),
			'LAM-DONG'        => __( 'Lam Dong', 'woo-viet' ),
			'LONG-AN'         => __( 'Long An', 'woo-viet' ),
			'NAM-DINH'        => __( 'Nam Dinh', 'woo-viet' ),
			'NGHE-AN'         => __( 'Nghe An', 'woo-viet' ),
			'NINH-BINH'       => __( 'Ninh Binh', 'woo-viet' ),
			'NINH-THUAN'      => __( 'Ninh Thuan', 'woo-viet' ),
			'PHU-THO'         => __( 'Phu Tho', 'woo-viet' ),
			'PHU-YEN'         => __( 'Phu Yen', 'woo-viet' ),
			'QUANG-BINH'      => __( 'Quang Binh', 'woo-viet' ),
			'QUANG-NAM'       => __( 'Quang Nam', 'woo-viet' ),
			'QUANG-NGAI'      => __( 'Quang Ngai', 'woo-viet' ),
			'QUANG-NINH'      => __( 'Quang Ninh', 'woo-viet' ),
			'QUANG-TRI'       => __( 'Quang Tri', 'woo-viet' ),
			'SOC-TRANG'       => __( 'Soc Trang', 'woo-viet' ),
			'SON-LA'          => __( 'Son La', 'woo-viet' ),
			'TAY-NINH'        => __( 'Tay Ninh', 'woo-viet' ),
			'THAI-BINH'       => __( 'Thai Binh', 'woo-viet' ),
			'THAI-NGUYEN'     => __( 'Thai Nguyen', 'woo-viet' ),
			'THANH-HOA'       => __( 'Thanh Hoa', 'woo-viet' ),
			'THUA-THIEN-HUE'  => __( 'Thua Thien - Hue', 'woo-viet' ),
			'TIEN-GIANG'      => __( 'Tien Giang', 'woo-viet' ),
			'TRA-VINH'        => __( 'Tra Vinh', 'woo-viet' ),
			'TUYEN-QUANG'     => __( 'Tuyen Quang', 'woo-viet' ),
			'VINH-LONG'       => __( 'Vinh Long', 'woo-viet' ),
			'VINH-PHUC'       => __( 'Vinh Phuc', 'woo-viet' ),
			'YEN-BAI'         => __( 'Yen Bai', 'woo-viet' ),
		);

		return $states;

	}
}