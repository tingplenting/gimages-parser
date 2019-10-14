package main

import (
	"encoding/json"
	"fmt"
	"io/ioutil"
	"net/http"
	"regexp"
)

func main() {

	// Json struct
	type Image struct {
		Ou string
	}

	term := "floral+wallpaper"
	htmlBody := getGoogleImage(term)

	// find json data within html page
	r, _ := regexp.Compile(`(?im){[^{}]*?"id".*?}`)
	imgData := r.FindAllString(htmlBody, -1)

	var image Image
	for _, value := range imgData {
		json.Unmarshal([]byte(value), &image)
		fmt.Println("Url: ", image.Ou)
	}

}

func getGoogleImage(term string) string {

	url := "https://www.google.com/search?q=" + term + "&client=firefox-b&source=lnms&tbm=isch"
	req, err := http.NewRequest("GET", url, nil)
	// handle the error if there is one
	if err != nil {
		panic(err)
	}

	req.Header.Set("User-Agent", "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/537.36")
	req.Header.Add("Referer", "localhost")
	req.Header.Add("Accept", "text/xml,application/xml,application/xhtml+xml,")
	req.Header.Add("Cache-Control", "max-age=0")
	req.Header.Add("Connection", "keep-alive")
	req.Header.Add("Accept-Charset", "ISO-8859-1,utf-8;q=0.7,*;q=0.7")
	req.Header.Add("Accept-Language", "en-us,en;q=0.5")
	req.Header.Add("Pragma", "no-cache")

	client := &http.Client{}
	res, err := client.Do(req)
	if err != nil {
		panic(err)
	}

	// do this now so it won't be forgotten
	defer res.Body.Close()
	// reads html as a slice of bytes
	html, err := ioutil.ReadAll(res.Body)
	if err != nil {
		panic(err)
	}
	// return HTML code as a string %s
	return fmt.Sprintf("%s\n", html)
}
