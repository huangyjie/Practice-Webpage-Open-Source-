#include <stdio.h>  

int main() {  
    int N;   
    scanf("%d", &N);       

    int arr[N];           
    int l = 0;  
    int r = N - 1;  
    int s = 0;             
    int d = 0;             

    for (int i = 0; i < N; i++) {  
        arr[i] = (i + 1) % 2 == 1 ? 1 : 0;  
    }  

    int f = 1;             
    while (l <= r) {         
        if (arr[l] > arr[r]) {  
            if (f) {  
                s += arr[l]; 
            } else {  
                d += arr[l];
            }  
            l++;  
        } else {  
            if (f) {  
                s += arr[r]; 
            } else {  
                d += arr[r]; 
            }  
            r--;  
        }  
        f = !f;      
    }  

    printf("%d", s);

    return 0;  
}
