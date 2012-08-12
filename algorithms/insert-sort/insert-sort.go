package main
import fmt "fmt"

func yun_insert_sort_int(arr []int) {
    var key, value, prev_key, length int
    length = len(arr)
    for key=1; key<length; key++ {
        value = arr[key]
        
        for prev_key=key-1; prev_key>=0 && arr[prev_key]>value; prev_key-- {
            arr[prev_key+1] = arr[prev_key]
        }
        
        arr[prev_key+1] = value;
    }
}


func main() {
    arr := [5]int{9,6,3,4,5}
    for_sort := arr[:]
    yun_insert_sort_int(for_sort);
    fmt.Println(arr)
}
