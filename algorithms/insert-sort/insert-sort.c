#include <stdio.h>

void yun_insert_sort_int(int arr[], int length) {
  int key, value, i;
  for (key=1; key<length; key++) {
      value = arr[key];
      i = key-1;
      while (i>=0 && arr[i]>value) {
          arr[i+1] = arr[i];
          i--;
      }
      arr[i+1] = value;
  }
}

int main() {
  int arr[] = {9,6,5,4,3};
  int length;
  yun_insert_sort_int(arr, 5);
  for (length = 0; length < 5; length++) {
      printf("%d\n", arr[length]);
  }
  return 0;
}

