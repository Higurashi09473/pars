package main

import (
	"crypto/md5"
	"encoding/hex"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"sort"
)

func arr_in_str(params map[string]string) string {
	var stringi string
	keys := make([]string, 0, len(params))
	for k := range params {
		keys = append(keys, k)
	}
	sort.Strings(keys)

	for _, k := range keys {
		stringi += fmt.Sprint(k, "=", params[k])
	}
	return stringi
}

func GetMD5Hash(text string) string {
	hash := md5.Sum([]byte(text))
	return hex.EncodeToString(hash[:])
}

func main() {
	conf := New()
	params := map[string]string{
		"application_key": conf.public,
		"method":          "mediatopic.post",
		"gid":             "55962941456457",
		"type":            "GROUP_THEME",
		"attachment":      `{"media": [{ "type": "text", "text": "This is a text of a new topic" }]}`,
		"format":          "json",
	}
	g := fmt.Sprint(`https://api.ok.ru/fb.do?application_key=CBMFPMLGDIHBABABA&attachment=%7B%22media%22%3A%20%5B%7B%20%22type%22%3A%20%22text%22%2C%20%22text%22%3A%20%22This%20is%20a%20text%20of%20a%20new%20topic%22%20%7D%5D%7D&format=json&gid=55962941456457&method=mediatopic.post&type=GROUP_THEME&sig=98b627bfd2c2b6143b3d9e5602c5e92a&access_token=-n-13WmyQgHl1TbN7rISCUAR7Xv6Pewy5LxhdWwOO9pIOX1OSTsNEwUONcPuJx4qqZxf1Y2eIFUSb1CZysx0`)
	sig := GetMD5Hash(arr_in_str(params) + conf.session)
	params["access_token"] = conf.token
	params["sig"] = sig

	data, err := json.Marshal(params)
	if err != nil {
		panic(err)
	}

	fmt.Println(string(data))

	req, err := http.NewRequest("GET", g, nil)
	//req, err := http.Get("POST", "https://api.ok.ru/fb.do", bytes.NewBuffer(data)) //Отправляем пост
	req.Header.Set("Content-Type", "application/json; charset=UTF-8")
	if err != nil {
		panic(err)
	}

	client := &http.Client{}
	res, err := client.Do(req)
	if err != nil {
		panic(err)
	}
	defer res.Body.Close()

	fmt.Println("\nResponse Status", res.Status)
	fmt.Println("\nHeader", res.Header)
	body, _ := io.ReadAll(res.Body)
	fmt.Println("\nResponse Body", string(body))
}
