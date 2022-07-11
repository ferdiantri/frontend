import React from 'react'
import { Image, StyleSheet, View } from 'react-native'

export default function EmptyCart() {
  return(
      <View style={styles.container}>
          <Image source={require('../assets/emptycart.png')} style={styles.image} />
      </View>
  )
}

const styles = StyleSheet.create({
    container: {
        flexDirection: 'column',
        justifyContent: 'center',
        alignItems: 'center',
        height: '100%'
    },
    image: {
        width: 150,
        height: 150,
    },
})
