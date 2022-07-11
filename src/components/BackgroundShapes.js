import React from 'react'
import { ImageBackground, StyleSheet, KeyboardAvoidingView } from 'react-native'
import { theme } from '../core/theme'
import {Shapes} from "react-native-background-shapes";

export default function Background({ children }) {
  return (
    <Shapes
    primaryColor="#416DF8"
    secondaryColor="#2F53D5"
    height={3}
    borderRadius={20}
    figures={[
    {name: 'circle', position: 'center', size: 60},
    {name: 'donut', position: 'flex-start', axis: 'top', size: 80},
    {name: 'circle', position: 'center', axis: 'right', size: 100},
    ]}
    />
  )
}

const styles = StyleSheet.create({
  background: {
    flex: 1,
    width: '100%',
    backgroundColor: '#2D31FA',
  },
  container: {
    flex: 1,
    padding: 20,
    width: '100%',
    maxWidth: 340,
    alignSelf: 'center',
    alignItems: 'center',
    justifyContent: 'center',
  },
})
