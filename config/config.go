package config

type config struct {
	token   string
	public  string
	session string
}

func New() config {
	var conf config
	conf.token = "-n-13WmyQgHl1TbN7rISCUAR7Xv6Pewy5LxhdWwOO9pIOX1OSTsNEwUONcPuJx4qqZxf1Y2eIFUSb1CZysx0"
	conf.public = "CBMFPMLGDIHBABABA"
	conf.session = "9876f792b60b2738f751319407a0dcca"
	return conf
}

// ok_access_token := "-n-13WmyQgHl1TbN7rISCUAR7Xv6Pewy5LxhdWwOO9pIOX1OSTsNEwUONcPuJx4qqZxf1Y2eIFUSb1CZysx0"
// ok_public_key := "CBMFPMLGDIHBABABA"
// ok_session_key := "9876f792b60b2738f751319407a0dcca"
